<?php

declare(strict_types = 1);

use Consolidation\AnnotatedCommand\Attributes\Argument;
use Consolidation\AnnotatedCommand\Attributes\Command;
use Consolidation\AnnotatedCommand\Attributes\Help;
use Consolidation\AnnotatedCommand\Attributes\Hook;
use Consolidation\AnnotatedCommand\Attributes\Option;
use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\AnnotatedCommand\CommandResult;
use Consolidation\AnnotatedCommand\Hooks\HookManager;
use League\Container\Container as LeagueContainer;
use NuvoleWeb\Robo\Task\Config\Robo\loadTasks as ConfigLoader;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Robo\Common\ConfigAwareTrait;
use Robo\Contract\ConfigAwareInterface;
use Robo\Contract\TaskInterface;
use Robo\Tasks;
use Sweetchuck\LintReport\Reporter\BaseReporter;
use Sweetchuck\Robo\Phpcs\PhpcsTaskLoader;
use Sweetchuck\Robo\PhpMessDetector\PhpmdTaskLoader;
use Sweetchuck\Robo\Phpstan\PhpstanTaskLoader;
use Sweetchuck\Utils\Filter\EnabledFilter;
use Sweetchuck\Utils\Tests\Attributes\InitLintReporters;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RoboFile extends Tasks implements LoggerAwareInterface, ConfigAwareInterface
{
    use LoggerAwareTrait;
    use ConfigAwareTrait;
    use ConfigLoader;
    use PhpcsTaskLoader;
    use PhpstanTaskLoader;
    use PhpmdTaskLoader;

    /**
     * @var array<string, mixed>
     */
    protected array $composerInfo = [];

    /**
     * @var string[]
     */
    protected array $testSuiteNames = [];

    protected string $packageVendor = '';

    protected string $packageName = '';

    protected string $binDir = 'vendor/bin';

    protected string $gitHook = '';

    protected string $envVarNamePrefix = '';

    /**
     * Allowed values: local, dev, ci, prod.
     */
    protected string $environmentType = '';

    /**
     * Allowed values: local, jenkins, travis, circleci.
     */
    protected string $environmentName = '';

    public function __construct()
    {
        $this
            ->initComposerInfo()
            ->initEnvVarNamePrefix()
            ->initEnvironmentTypeAndName();
    }

    protected function initComposerInfo(): static
    {
        if ($this->composerInfo) {
            return $this;
        }

        $composerFile = getenv('COMPOSER') ?: 'composer.json';
        $composerContent = file_get_contents($composerFile);
        if ($composerContent === false) {
            return $this;
        }

        $this->composerInfo = json_decode($composerContent, true);
        [$this->packageVendor, $this->packageName] = explode('/', $this->composerInfo['name']);

        if (!empty($this->composerInfo['config']['bin-dir'])) {
            $this->binDir = $this->composerInfo['config']['bin-dir'];
        }

        return $this;
    }

    protected function initEnvVarNamePrefix(): static
    {
        $this->envVarNamePrefix = strtoupper(str_replace('-', '_', $this->packageName));

        return $this;
    }

    protected function initEnvironmentTypeAndName(): static
    {
        $this->environmentType = (string) getenv($this->getEnvVarName('environment_type'));
        $this->environmentName = (string) getenv($this->getEnvVarName('environment_name'));

        if (!$this->environmentType) {
            if (getenv('CI') === 'true') {
                // CircleCI, Travis and GitLab.
                $this->environmentType = 'ci';
            } elseif (getenv('JENKINS_HOME')) {
                $this->environmentType = 'ci';
                if (!$this->environmentName) {
                    $this->environmentName = 'jenkins';
                }
            }
        }

        if (!$this->environmentName && $this->environmentType === 'ci') {
            if (getenv('GITLAB_CI') === 'true') {
                $this->environmentName = 'gitlab';
            } elseif (getenv('TRAVIS') === 'true') {
                $this->environmentName = 'travis';
            } elseif (getenv('CIRCLECI') === 'true') {
                $this->environmentName = 'circle';
            }
        }

        if (!$this->environmentType) {
            $this->environmentType = 'dev';
        }

        if (!$this->environmentName) {
            $this->environmentName = 'local';
        }

        return $this;
    }

    #[Hook(
        type: HookManager::PRE_COMMAND_HOOK,
        selector: InitLintReporters::SELECTOR,
    )]
    public function onHookPreCommandInitLintReporters(): void
    {
        $lintServices = BaseReporter::getServices();
        $container = $this->getContainer();
        if (!($container instanceof LeagueContainer)) {
            return;
        }

        foreach ($lintServices as $name => $class) {
            if ($container->has($name)) {
                continue;
            }

            $container
                ->add($name, $class)
                ->setShared(false);
        }
    }

    /**
     * @phpstan-param array<string, mixed> $options
     */
    #[Command(name: 'environment:info')]
    #[Help(
        description: 'Exports the curren environment info.',
        hidden: true,
    )]
    #[Option(
        name: 'format',
        description: 'Output format.',
    )]
    public function cmdEnvironmentInfoExecute(
        array $options = [
            'format' => 'yaml',
        ],
    ): CommandResult {
        return CommandResult::dataWithExitCode(
            [
                'type' => $this->environmentType,
                'name' => $this->environmentName,
            ],
            0,
        );
    }

    #[Command(name: 'githook:pre-commit')]
    #[Help(
        description: 'Git "pre-commit" hook callback.',
        hidden: true,
    )]
    #[InitLintReporters]
    public function cmdGitHookPreCommitExecute(): TaskInterface
    {
        $this->gitHook = 'pre-commit';

        return $this
            ->collectionBuilder()
            ->addTaskList(array_filter([
                'composer.validate' => $this->taskComposerValidate(),
                'circleci.config.validate' => $this->getTaskCircleCiConfigValidate(),
                'phpcs.lint' => $this->getTaskPhpcsLint(),
                'phpstan.analyze' => $this->getTaskPhpstanAnalyze(),
                'phpunit.run' => $this->getTaskPhpunitRunSuites(),
            ]));
    }

    #[Hook(
        type: HookManager::ARGUMENT_VALIDATOR,
        target: 'test',
    )]
    public function cmdTestValidate(CommandData $commandData): void
    {
        $input = $commandData->input();
        $suiteNames = $input->getArgument('suiteNames');
        if ($suiteNames) {
            $invalidSuiteNames = array_diff($suiteNames, $this->getTestSuiteNames());
            if ($invalidSuiteNames) {
                throw new InvalidArgumentException(
                    'The following PhpUnit suite names are invalid: ' . implode(', ', $invalidSuiteNames),
                    1,
                );
            }
        }
    }

    /**
     * @param string[] $suiteNames
     */
    #[Command(name: 'test')]
    #[Help(
        description: 'Runs tests.',
    )]
    #[Argument(
        name: 'suiteNames',
        description: 'Suite names',
    )]
    public function cmdTestExecute(array $suiteNames): TaskInterface
    {
        return $this->getTaskPhpunitRunSuites($suiteNames);
    }

    #[Command(name: 'lint')]
    #[Help(
        description: 'Runs code style checkers.',
    )]
    #[InitLintReporters]
    public function cmdLintExecute(): TaskInterface
    {
        return $this
            ->collectionBuilder()
            ->addTaskList(array_filter([
                'composer.validate' => $this->taskComposerValidate(),
                'circleci.config.validate' => $this->getTaskCircleCiConfigValidate(),
                'phpcs.lint' => $this->getTaskPhpcsLint(),
                'phpstan.analyze' => $this->getTaskPhpstanAnalyze(),
            ]));
    }

    #[Command(name: 'lint:phpcs')]
    #[Help(
        description: 'Runs phpcs.',
    )]
    #[InitLintReporters]
    public function cmdLintPhpcsExecute(): TaskInterface
    {
        return $this->getTaskPhpcsLint();
    }

    #[Command(name: 'lint:phpstan')]
    #[Help(
        description: 'Runs phpstan analyze.',
    )]
    #[InitLintReporters]
    public function cmdLintPhpstanExecute(): TaskInterface
    {
        return $this->getTaskPhpstanAnalyze();
    }

    #[Command(name: 'lint:phpmd')]
    #[Help(
        description: 'Runs phpmd.',
    )]
    #[InitLintReporters]
    public function cmdLintPhpmdExecute(): TaskInterface
    {
        return $this->getTaskPhpmdLint();
    }

    #[Command(name: 'lint:circleci-config')]
    #[Help(
        description: 'Runs circleci validate.',
    )]
    public function cmdLintCircleciConfigExecute(): ?TaskInterface
    {
        return $this->getTaskCircleCiConfigValidate();
    }

    protected function getTaskCircleCiConfigValidate(): ?TaskInterface
    {
        if ($this->environmentType === 'ci') {
            return null;
        }

        return $this->taskExec('circleci --skip-update-check config validate');
    }

    protected function errorOutput(): ?OutputInterface
    {
        $output = $this->output();

        return ($output instanceof ConsoleOutputInterface) ? $output->getErrorOutput() : $output;
    }

    protected function getEnvVarName(string $name): string
    {
        return "{$this->envVarNamePrefix}_" . strtoupper($name);
    }

    /**
     * @param string[] $suiteNames
     */
    protected function getTaskPhpunitRunSuites(array $suiteNames = []): TaskInterface
    {
        if (!$suiteNames) {
            $suiteNames = ['all'];
        }

        /** @phpstan-var array<string, php-executable> $phpExecutables */
        $phpExecutables = array_filter(
            $this->getConfig()->get('php.executables'),
            new EnabledFilter(),
        );

        $cb = $this->collectionBuilder();
        foreach ($suiteNames as $suiteName) {
            foreach ($phpExecutables as $phpExecutable) {
                $cb->addTask($this->getTaskPhpUnitRunSuite($suiteName, $phpExecutable));
            }
        }

        return $cb;
    }

    /**
     * @phpstan-param php-executable $php
     */
    protected function getTaskPhpUnitRunSuite(string $suite, array $php): TaskInterface
    {
        $cmdPattern = '';
        $cmdArgs = [];
        foreach ($php['envVars'] ?? [] as $envName => $envValue) {
            $cmdPattern .= "{$envName}";
            if ($envValue === null) {
                $cmdPattern .= ' ';
            } else {
                $cmdPattern .= '=%s ';
                $cmdArgs[] = escapeshellarg($envValue);
            }
        }

        $cmdPattern .= '%s';
        $cmdArgs[] = $php['command'];

        $cmdPattern .= ' %s';
        $cmdArgs[] = escapeshellcmd("{$this->binDir}/phpunit");

        $cb = $this->collectionBuilder();
        if ($suite !== 'all') {
            $cmdPattern .= ' --testsuite=%s';
            $cmdArgs[] = escapeshellarg($suite);
        }

        if ($this->environmentType === 'ci' && $this->environmentName === 'jenkins') {
            // Jenkins has to use a post-build action to mark the build "unstable".
            $cmdPattern .= ' || [[ "${?}" == "1" ]]';
        }

        $command = vsprintf($cmdPattern, $cmdArgs);

        return $cb
            ->addCode(function () use ($command, $php) {
                $this->output()->writeln(strtr(
                    '<question>[{name}]</question> runs <info>{command}</info>',
                    [
                        '{name}' => 'PhpUnit',
                        '{command}' => $command,
                    ]
                ));

                $process = Process::fromShellCommandline(
                    $command,
                    null,
                    $php['envVars'] ?? null,
                    null,
                    null,
                );

                return $process->run(function ($type, $data) {
                    switch ($type) {
                        case Process::OUT:
                            $this->output()->write($data);
                            break;

                        case Process::ERR:
                            $this->errorOutput()->write($data);
                            break;
                    }
                });
            });
    }

    protected function getTaskPhpcsLint(): TaskInterface
    {
        $options = [
            'failOn' => 'warning',
            'lintReporters' => [
                'lintVerboseReporter' => null,
            ],
        ];

        if ($this->environmentType === 'ci' && $this->environmentName === 'jenkins') {
            $options['failOn'] = 'never';
            $options['lintReporters']['lintCheckstyleReporter'] = $this
                ->getContainer()
                ->get('lintCheckstyleReporter')
                ->setDestination('tests/_output/machine/checkstyle/phpcs.psr2.xml');
        }

        return $this->taskPhpcsLintFiles($options);
    }

    protected function getTaskPhpstanAnalyze(): TaskInterface
    {
        if ($this->environmentName === 'circle') {
            return $this
                ->collectionBuilder()
                ->addCode(function (): int {
                    $this->yell(
                        "CircleCI and PHPStan aren't friends. Looks like memory_limit problem.",
                        40,
                        'red',
                    );

                    return 0;
                });
        }

        /** @var \Sweetchuck\LintReport\Reporter\VerboseReporter $verboseReporter */
        $verboseReporter = $this->getContainer()->get('lintVerboseReporter');
        $verboseReporter->setFilePathStyle('relative');

        return $this
            ->taskPhpstanAnalyze()
            ->setNoProgress(true)
            ->setNoInteraction(true)
            ->setErrorFormat('json')
            ->addLintReporter('lintVerboseReporter', $verboseReporter);
    }

    protected function getTaskPhpmdLint(): TaskInterface
    {
        $task = $this
            ->taskPhpmdLintFiles()
            ->setInputFile('./rulesets/custom.include-pattern.txt')
            ->addExcludePathsFromFile('./rulesets/custom.exclude-pattern.txt')
            ->setRuleSetFileNames(['custom']);
        $task->setOutput($this->output());

        return $task;
    }

    protected function getReportsDir(): string
    {
        return 'reports';
    }

    /**
     * @return string[]
     */
    protected function getTestSuiteNames(): array
    {
        if (!$this->testSuiteNames) {
            $this->testSuiteNames = [
                'unit',
            ];
        }

        return $this->testSuiteNames;
    }
}
