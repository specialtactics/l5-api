<?php

namespace Specialtactics\L5Api\Console\Commands\Laravel;

class SeederMakeCommand extends \Illuminate\Database\Console\Seeds\SeederMakeCommand
{
    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        $boilerplateStub = __DIR__. '/../../../../resources' .$stub;

        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : (file_exists($boilerplateStub) ? $boilerplateStub : parent::resolveStubPath($stub));
    }
}
