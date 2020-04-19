<?php

namespace Stan\Services\CodebaseModel;

use PhpParser\Error as ParseError;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

class Project {
    protected $classes      = [];
    protected $interfaces   = [];
    protected $traits       = [];
    protected $functions    = [];
    protected $constants    = [];

    public function __construct(
        ParserFactory $parserFactory,
        NodeDumper $nodeDumper
    ) {
        $this->parserFactory = $parserFactory;
        $this->nodeDumper = $nodeDumper;

        $this->classes      = [];
        $this->interfaces   = [];
        $this->traits       = [];
        $this->functions    = [];
        $this->constants    = [];
    }

    public function addFileToModel($filename) {
        $parser = $this->parserFactory->create(ParserFactory::PREFER_PHP7);
        $code = file_get_contents($filename);
        try {
            $ast = $parser->parse($code);
            // printf("Parsed file %s: %s", $filename, $this->nodeDumper->dump($ast));
        } catch (ParseError $e) {
            printf("Parse error in file %s: %s\n", $filename, $e);
        }
    }
}
