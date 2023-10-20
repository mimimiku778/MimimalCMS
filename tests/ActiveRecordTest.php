<?php

use PHPUnit\Framework\TestCase;
use App\Models\ActiveRecord;
use Shadow\DB;

/** 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class ActiveRecordTest extends TestCase
{
    // Define table names for testing
    private const TEST_TABLE = 'test_table';

    // Teardown: Remove the test table from the database
    protected function tearDown(): void
    {
        // Drop the test table
        DB::$pdo->exec("DROP TABLE " . self::TEST_TABLE);
    }

    public function testInsert()
    {
        $tableName = self::TEST_TABLE;

        // Create a PDO instance and set up the test database
        DB::connect();

        DB::$pdo->exec("
            CREATE TABLE {$tableName} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                email VARCHAR(255)
            )
        ");

        // Create test data
        $testData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];

        // Create an ActiveRecord instance
        $record = new ActiveRecord();
        foreach ($testData as $key => $value) {
            $record->$key = $value;
        }

        // Call the method under test
        $lastInsertId = $record->insert($tableName);

        // Verify the expected result
        debug(DB::fetch("SELECT * FROM {$tableName}"));

        $this->assertEquals(1, $lastInsertId);
    }

    public function testFindRecords()
    {
        $tableName = self::TEST_TABLE;

        // Create a PDO instance and set up the test database
        DB::connect();

        DB::$pdo->exec("CREATE TABLE {$tableName} (id INT PRIMARY KEY, name VARCHAR(255))");

        DB::$pdo->exec("INSERT INTO {$tableName} (id, name) VALUES (1, 'John'), (2, 'Doe')");

        // Set up the test query and parameters
        $query = "SELECT * FROM {$tableName}";
        $params = null;

        // Call the findRecords method
        $result = ActiveRecord::findRecords($query, $params);

        // Verify the expected result
        debug($result);

        $this->assertCount(2, $result);
        $this->assertEquals('John', $result[0]->name);
        $this->assertEquals('Doe', $result[1]->name);
    }

    public function testSearchRecords()
    {
        $tableName = self::TEST_TABLE;

        // Create a PDO instance and set up the test database
        DB::connect();

        DB::$pdo->exec("CREATE TABLE {$tableName} (id INT PRIMARY KEY, name VARCHAR(255))");

        DB::$pdo->exec("INSERT INTO {$tableName} (id, name) VALUES (1, 'John'), (2, 'Doe')");

        // Set up the test query and parameters
        $query = function (string $where) use ($tableName) {
            return "SELECT * FROM {$tableName} {$where}";
        };

        $whereClauseQuery = function (int $i) {
            return "(name LIKE :keyword{$i})";
        };

        $keyword = 'Jo hn';

        // Call the searchRecords method
        $result = ActiveRecord::searchRecords($query, $whereClauseQuery, $keyword);

        // Verify the expected result
        debug($result);

        $this->assertCount(1, $result);
        $this->assertEquals('John', $result[0]->name);
    }
}
