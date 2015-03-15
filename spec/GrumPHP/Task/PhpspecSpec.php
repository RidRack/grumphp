<?php

namespace spec\GrumPHP\Task;

use GrumPHP\Configuration\ConfigurationInterface;
use GrumPHP\Configuration\GrumPHP;
use GrumPHP\Locator\LocatorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PhpspecSpec extends ObjectBehavior
{
    function let(GrumPHP $grumPHP, ConfigurationInterface $configuration, LocatorInterface $externalCommandLocator, ProcessBuilder $processBuilder)
    {
        $this->beConstructedWith($grumPHP, $configuration, $externalCommandLocator, $processBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('GrumPHP\Task\Phpspec');
    }

    function it_is_a_grumphp_external_task()
    {
        $this->shouldHaveType('GrumPHP\Task\ExternalTaskInterface');
    }

    function it_uses_its_external_command_locator_to_find_correct_command(LocatorInterface $externalCommandLocator)
    {
        $externalCommandLocator->locate('phpspec')->shouldBeCalled();
        $this->getCommandLocation();
    }

    function it_runs_the_suite(ProcessBuilder $processBuilder, Process $process)
    {
        $processBuilder->setArguments(Argument::type('array'))->shouldBeCalled();
        $processBuilder->getProcess()->willReturn($process);

        $process->run()->shouldBeCalled();
        $process->isSuccessful()->willReturn(true);

        $this->run(array());
    }

    function it_throws_exception_if_the_process_fails(ProcessBuilder $processBuilder, Process $process)
    {
        $processBuilder->setArguments(Argument::type('array'))->shouldBeCalled();
        $processBuilder->getProcess()->willReturn($process);

        $process->run()->shouldBeCalled();
        $process->isSuccessful()->willReturn(false);
        $process->getOutput()->shouldBeCalled();

        $this->shouldThrow('GrumPHP\Exception\RuntimeException')->duringRun(array());
    }
}