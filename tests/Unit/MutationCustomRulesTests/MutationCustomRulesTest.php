<?php

declare(strict_types = 1);
namespace Rebing\GraphQL\Tests\Unit\MutationCustomRulesTests;

use Illuminate\Contracts\Support\MessageBag;
use Rebing\GraphQL\Tests\TestCase;

class MutationCustomRulesTest extends TestCase
{
    public function testMutationWithCustomRuleWithClosure(): void
    {
        $graphql = <<<'GRAPHQL'
mutation Mutate($arg1: String) {
  mutationWithCustomRuleWithClosure(arg1: $arg1)
}
GRAPHQL;

        $result = $this->graphql($graphql, [
            'expectErrors' => true,
            'variables' => [
                'arg1' => 'Test argument 1',
            ],
        ]);

        self::assertCount(1, $result['errors']);
        self::assertSame('validation', $result['errors'][0]['message']);
        /** @var MessageBag $messageBag */
        $messageBag = $result['errors'][0]['extensions']['validation'];
        self::assertSame(['arg1 is invalid'], $messageBag->all());
    }

    public function testMutationWithCustomRuleWithRuleObject(): void
    {
        $graphql = <<<'GRAPHQL'
mutation Mutate($arg1: String) {
  mutationWithCustomRuleWithRuleObject(arg1: $arg1)
}
GRAPHQL;

        $result = $this->graphql($graphql, [
            'expectErrors' => true,
            'variables' => [
                'arg1' => 'Test argument 1',
            ],
        ]);

        self::assertCount(1, $result['errors']);
        self::assertSame('validation', $result['errors'][0]['message']);
        /** @var MessageBag $messageBag */
        $messageBag = $result['errors'][0]['extensions']['validation'];
        self::assertSame(['arg1 is invalid'], $messageBag->all());
    }

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('graphql.schemas.default', [
            'mutation' => [
                MutationWithCustomRuleWithClosure::class,
                MutationWithCustomRuleWithRuleObject::class,
            ],
        ]);
    }
}
