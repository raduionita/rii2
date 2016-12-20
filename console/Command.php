<?php

namespace rii\console;

abstract class Command
{
    protected $name;
    protected $description;

    abstract public function run() : void;
}