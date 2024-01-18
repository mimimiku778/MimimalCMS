<?php

use PHPUnit\Framework\TestCase;
use Shadow\DB;

class Testy
{
    public $id;
    public $value;
}

/** 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class PDOTest extends TestCase
{
    function test()
    {
        $result = DB::fetchAll('select * from test', args: [PDO::FETCH_CLASS, Testy::class]);

        debug($result);
        
        $this->assertEquals(0, 0);
    }
}
