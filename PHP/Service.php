<?php

namespace Jp\Auto;

use Exception;
use Jp\Models\Auto\Chassis;
use Jp\Models\Auto\Engine;
use Jp\Models\Auto\Mark;
use Jp\Models\Auto\Model;
use Jp\Cache\ProxyCache;

class AutoService
{
    private const CACHE_TTL_SECONDS = 60 * 60 * 24 * 1;

    /** @var AutoRepository */
    private $autoRepository;

    public function __construct(bool $useCache = true)
    {
        if ($useCache) {
            $this->autoRepository = new ProxyCache(new AutoRepository(), self::CACHE_TTL_SECONDS, 'auto.repository');
        } else {
            $this->autoRepository = new AutoRepository();
        }
    }

    /**
     * @param $products
     * @param null $analogs
     * @return array
     */
    public function getuniquebrands($products, $analogs = null):array
    {
        $brands = array();
        if(!is_null($analogs)){
           foreach($analogs as $analog){
               if(!in_array($analog->getBrand(),$brands)){
                   $brands[]= $analog->getBrand();
               }
           }
        }
        if(!is_null($products)){
            foreach($products as $product){
                if(!in_array($product->getBrand(),$brands)){
                    $brands[]= $product->getBrand();
                }
            }
        }

        return $brands;
    }

    /**
     * @return Mark[] array
     */
    public function getMarks(): array
    {
        return $this->autoRepository->getMarks();
    }

    /**
     * @param int $id
     * @return Mark
     * @throws Exception
     */
    public function getMarkById(int $id): Mark
    {
        foreach ($this->getMarks() as $mark) {
            if ($mark->getId() === $id) {
                return $mark;
            }
        }

        throw new Exception('Mark not found');
    }

    /**
     * @param int|null $id
     * @return Model|null
     * @throws Exception
     */
    public function getModelById(?int $id): ?Model
    {
        if(is_null($id)){
            return null;
        }else{
            return $this->autoRepository->getModelById($id);
        }
    }

    public function getModelsByMark(Mark $mark): array
    {
        return $this->autoRepository->getModelsByMark($mark);
    }

    /**
     * @param string $name
     * @return Chassis[] array|null
     * @throws Exception
     */
    public function findChassisLikeName(string $name): ?array
    {
        return $this->autoRepository->findChassisLikeName($name);
    }

    /**
     * @param string $name
     * @return Chassis|null
     * @throws Exception
     */
    public function findChassisByName(string $name): ?Chassis
    {
        return $this->autoRepository->findChassisByName($name);
    }

    /**
     * @param Model $model
     * @param Chassis $chassis
     * @return Engine[]
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function getEnginesByModelAndChassis(Model $model, Chassis $chassis): array
    {
        return $this->autoRepository->getEnginesByModelAndChassis($model, $chassis);
    }

    /**
     * @param Mark $mark
     * @param Model $model
     * @return array
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function getEnginesByMarkModel(Mark $mark, Model $model): array
    {
        return $this->autoRepository->getEnginesByMarkModel($mark, $model);
    }

    /**
     * @param int $id
     * @return Engine
     * @throws Exception
     */
    public function getEngineById(int $id): Engine
    {
        return $this->autoRepository->getEngineById($id);
    }

    /**
     * @param Mark $mark
     * @param Model $model
     * @return Chassis[]
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function getChassisByMarkModel(Mark $mark, Model $model): array
    {
        return $this->autoRepository->getChassisByMarkModel($mark, $model);
    }

    /**
     * @param int $id
     * @return Chassis
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function getChassisById(int $id): Chassis
    {
        return $this->autoRepository->getChassisById($id);
    }

    public function removeMarkModelFromString(string $string): string
    {
        foreach ($this->getMarks() as $mark) {
            $string = trim(str_ireplace($mark->getName(), '', $string));

            foreach ($this->getModelsByMark($mark) as $model) {
                $string = trim(str_ireplace($model->getName(), '', $string));
            }
        }

        return $string;
    }
}
