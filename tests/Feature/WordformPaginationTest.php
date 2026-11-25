<?php

namespace Tests;

use App\Http\Controllers\Dict\WordformController;
use Illuminate\Http\Request;

class WordformPaginationTest extends IsolatedSqliteTestCase
{
    /**
     * @dataProvider rejectedPaginationProvider
     */
    public function testRejectedPaginationDoesNotExecuteSql($parameters, $message_key)
    {
        $this->app->setLocale('en');

        $request = Request::create('/en/dict/wordform', 'GET', $parameters);
        $controller = new WordformController($request);
        $connection = self::assertSqliteIsolation($this->app);

        $connection->enableQueryLog();
        $connection->flushQueryLog();

        $response = $controller->index($request);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(trans($message_key), $response->getContent());
        $this->assertSame([], $connection->getQueryLog());
    }

    public function rejectedPaginationProvider()
    {
        return [
            'observed_deep_page' => [['page' => '345464', 'limit_num' => '10'], 'pagination.depth_limited'],
            'offset_10010' => [['page' => '1002', 'limit_num' => '10'], 'pagination.depth_limited'],
            'offset_10100' => [['page' => '102', 'limit_num' => '100'], 'pagination.depth_limited'],
            'oversized_page_number' => [['page' => '999999999999999999999999', 'limit_num' => '10'], 'pagination.invalid_parameters'],
            'page_size_101' => [['page' => '1', 'limit_num' => '101'], 'pagination.invalid_parameters'],
            'page_zero' => [['page' => '0'], 'pagination.invalid_parameters'],
            'page_negative' => [['page' => '-1'], 'pagination.invalid_parameters'],
            'page_fraction' => [['page' => '1.5'], 'pagination.invalid_parameters'],
            'page_text' => [['page' => 'abc'], 'pagination.invalid_parameters'],
            'page_array' => [['page' => ['1']], 'pagination.invalid_parameters'],
            'page_size_zero' => [['limit_num' => '0'], 'pagination.invalid_parameters'],
            'page_size_array' => [['limit_num' => ['10']], 'pagination.invalid_parameters'],
        ];
    }
}
