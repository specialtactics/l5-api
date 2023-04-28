<?php

namespace Specialtactics\L5Api\Console\Commands\Laravel;

class PolicyMakeCommand extends \Illuminate\Foundation\Console\PolicyMakeCommand
{
    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__. '/../../../../resources' .$stub;
    }
}
