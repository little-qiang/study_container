<?php

namespace Lib;

class Visitor
{
	private $tool;

	public function __construct(ToolInterface $tool)
	{
		$this->tool = $tool;
	}

	public function go()
	{
		$this->tool->go();
	}
}