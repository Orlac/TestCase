<?php
declare(strict_types=1);

//==============Не редактировать
final class DataBase
{
    private bool $isConnected = false;

    public function connect(): bool
    {
        sleep(1);
        $this->isConnected = true;
        /**
         * Fatal error: Uncaught TypeError: DataBase::connect(): Return value must be of type bool, string returned in /var/www/TestCase/Program.php:14
         */
        //return 'connected';
        return true;
    }

    public function random()
    {
        $this->isConnected = rand(0, 3) ? $this->isConnected : false;
    }

    public function fetch($id): string
    {
        $this->random();
        if (!$this->isConnected) {
            throw new Exception('No connection');
        }
        usleep(100000);
        return 'fetched - ' . $id;
    }

    public function insert($data): string
    {
        $this->random();
        if (!$this->isConnected) {
            throw new Exception('No connection');
        }
        usleep(900000);
        return 'inserted - ' . $data;
    }


    public function batchInsert($data): string
    {
        $this->random();
        if (!$this->isConnected) {
            throw new Exception('No connection');
        }
        usleep(900000);
        return 'batch inserted';
    }
}
//==============

class DataBaseHelper
{
    private static ?DataBaseHelper $instance;

    private function  __construct(protected readonly DataBase $dataBase)
    {
        $this->dataBase->connect();
    }

    public static function instance(DataBase $dataBase = new DataBase()): static
    {
        return self::$instance ?? (self::$instance = new static($dataBase));
    }

    public function fetch(int $id): string
    {
        return (string)$this->exec(function () use ($id) {
            return $this->dataBase->fetch($id);
        });
    }

    public function batchInsert(array $batch): string
    {
        return (string)$this->exec(function () use ($batch) {
            return $this->dataBase->batchInsert($batch);
        });
    }

    protected function exec(Callable $fn): mixed
    {
        $count = 1;
        while (true) {
            try {
                return call_user_func($fn);
            } catch (Exception $exception) {
                $count++;
                echo $exception->getMessage() . PHP_EOL;
                print(sprintf('try connect %s', $count)) . PHP_EOL;
                $this->dataBase->connect();
            }
        }
    }
}

/**
 * @param int[] $dataToFetch
 * @return void
 */
function step1(array $dataToFetch): void
{
    $dataBaseHelper = DataBaseHelper::instance();

    for ($i = 0; $i < count($dataToFetch); $i++) {
        print($dataBaseHelper->fetch($dataToFetch[$i]));
        print(PHP_EOL);
    }
}

/**
 * @param int[] $dataToInsert
 * @return void
 */
function step2(array $dataToInsert): void
{
    print(DataBaseHelper::instance()->batchInsert($dataToInsert));
    print(PHP_EOL);
}

//==============Не редактировать
$dataToFetch = [1, 2, 3, 4, 5, 6];
$dataToInsert = [7, 8, 9, 10, 11, 12];

step1($dataToFetch);
step2($dataToInsert);
print("Success");
//==============