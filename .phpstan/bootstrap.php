<?php

exec('"$("${COMPOSER_BINARY:-composer}" config bin-dir)/codecept" build');
