<?php

interface Animal {

}

interface CanGiveMilk {
    public function getMilk(): int;
}

interface CanGiveEggs {
    public function getEggs(): int;
}

class Cow implements Animal, CanGiveMilk {

    public $id;
    public function __construct()
    {
        $this->id = substr(md5(rand()), 0, 6); //получаем случаный id длинною в 6 символов
    }

    public function getMilk(): int
    {
        return rand(8, 12); //выдает 8-12 литров молока
    }
}

class Hen implements Animal, CanGiveEggs {

    public $id;
    public function __construct()
    {
        $this->id = substr(md5(rand()), 0, 6); //получаем случаный id длинною в 6 символов
    }

    public function getEggs(): int
    {
        return rand(0, 1); //выдает 0-1 яичек
    }
}

interface Storage { //хранилище

    public function addMilk(int $liters);

    public function addEggs(int $eggsCount);

    public function getFreeSpaceForMilk(): int;

    public function getFreeSpaceForEggs(): int;

    public function howMuchMilk(): int;

    public function howMuchEggs(): int;

}

class Barn implements Storage { //амбар

    private $milkLiters = 0;
    private $eggsCount = 0;
    private $milkLimit = 0;
    private $eggsLimit = 0;

    public function __construct(int $milkLimit, int $eggsLimit)
    {
        $this->milkLimit = $milkLimit; //указываем максимальную вместимость по молоку
        $this->eggsLimit = $eggsLimit; //указываем максимальную вместимость по яйцам
    }

    public function addMilk(int $liters)
    {
        $freeSpace = $this->getFreeSpaceForMilk();

        if ($freeSpace === 0) { //абмар заполнен, места нет
          return;
        }

        if ($freeSpace < $liters) { //дозаполняем амбар, насколько хватает места
          $this->milkLiters = $this->milkLimit;
          return;
        }

        $this->milkLiters += $liters; //льем все молоко, что надоили
    }

    public function addEggs(int $eggsCount) //для яиц аналогичные действия
    {
        $freeSpace = $this->getFreeSpaceForEggs();

        if ($freeSpace === 0) {
            return;
        }

        if ($freeSpace < $eggsCount) {
          $this->eggsCount = $this->eggsLimit;
          return;
        }

        $this->eggsCount += $eggsCount;
    }

    public function getFreeSpaceForMilk(): int //считаем свободное место молоко
    {
        return $this->milkLimit - $this->milkLiters;
    }

    public function getFreeSpaceForEggs(): int //считаем свободное место яйца
    {
        return $this->eggsLimit - $this->eggsCount;
    }

    public function howMuchMilk(): int
    {
        return $this->milkLiters;
    }

    public function howMuchEggs(): int
    {
        return $this->eggsCount;
    }
}

class Farm { //класс фермы

    private $name;
    private $storage;
    private $animals = [];

    public function __construct(string $name, Storage $storage)
    {
        $this->name = $name;
        $this->storage = $storage;
    }

    public function returnMilk()
    {
        return $this->storage->howMuchMilk();

    }

    public function returnEggs()
    {
        return $this->storage->howMuchEggs();

    }

    public function addAnimal(Animal $animal)
    {
        $this->animals[] = $animal; //добавляем животное в массив
    }

    public function collectProducts() //сбор продукции
    {
        foreach ($this->animals as $animal)
        {
            if ($animal instanceOf CanGiveMilk) { //если относится к молокодающим, то сбор молока
                $milkLiters = $animal->getMilk();
                $this->storage->addMilk($milkLiters);
            }

            if ($animal instanceOf CanGiveEggs) { //с яйценесущих яйца
                $eggsCount = $animal->getEggs();
                $this->storage->addEggs($eggsCount);
            }
        }
    }
}

$barn = new Barn($milkLimit = 300, $eggsLimit = 500); //создаем амбар вместимостью 300 литров молока и 500 яичек

$myFarm = new Farm('MyFirstFarm', $barn);

for ($i=0;$i<40;$i++) {
    $myFarm->addAnimal(new Hen()); //сажаем в ферму куриц
}

for ($i=0;$i<10;$i++) {
    $myFarm->addAnimal(new Cow()); //и коров
}

$myFarm->collectProducts(); //собираем продукты

echo 'Молока надоено '.$myFarm->returnMilk().'<br>'; //выводим результат сбора
echo 'Яиц собрано '.$myFarm->returnEggs().'<br>';