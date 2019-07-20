<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09/02/2019
 * Time: 20:04
 */

namespace App\JobeetModule\Mapper;

use App\JobeetModule\Entity\Affiliate;
use Simplex\DataMapper\IdentifiableInterface;
use Simplex\DataMapper\Mapping\EntityMapper;

class AffiliateMapper extends EntityMapper
{

    /**
     * @var string
     */
    protected $table = 'affiliates';

    /**
     * Creates an entity from given input values
     *
     * @param array $input
     * @return Affiliate
     */
    public function createEntity(array $input): IdentifiableInterface
    {
        $affiliate = new Affiliate();
        $affiliate->setId($input['id']);
        $affiliate->setEmail($input['email']);
        $affiliate->setUrl($input['url']);
        $affiliate->setActive((bool)$input['is_active']);
        $affiliate->setName($input['name']);
        if (isset($input['token'])) {
            $affiliate->setToken($input['token']);
        }

        $categories = $input['categories'] ?? $this->query()
                ->nativeQuery()
                ->table('affiliate_category', 'p')
                ->where('affiliate_id', $input['id'])
                ->addSelect('p.category_id')
                ->get();

        foreach ($categories as $category) {
            $affiliate->addCategory($category['category_id']);
        }

        $this->uow->getIdentityMap()->add($affiliate, $affiliate->getId());

        return $affiliate;
    }

    /**
     * @param IdentifiableInterface|Affiliate $affiliate
     * @return mixed|void
     */
    public function insert(IdentifiableInterface $affiliate)
    {
        $insert = $this->extract($affiliate);

        $this->database->transaction(function () use ($insert, $affiliate) {
            $id = $this->query()->insert($insert);
            $affiliate->setId($id);

            $maps = [];
            foreach ($affiliate->getCategories() as $category) {
                $maps[] = [
                    'affiliate_id' => $id,
                    'category_id' => $category
                ];
            }

            if (!empty($maps)) {
                $this->query()
                    ->newQuery()
                    ->table('affiliate_category')
                    ->insert($maps);
            }
        });
    }

    /**
     * Extract an entity to persistable state
     *
     * @param IdentifiableInterface $affiliate
     * @return array
     */
    public function extract(IdentifiableInterface $affiliate): array
    {
        /** @var $affiliate Affiliate */
        return [
            'name' => $affiliate->getName(),
            'email' => $affiliate->getEmail(),
            'url' => $affiliate->getUrl(),
            'is_active' => $affiliate->isActive(),
            'token' => $affiliate->getToken()
        ];
    }

    /**
     * @param IdentifiableInterface $entity
     * @return mixed
     */
    public function update(IdentifiableInterface $entity)
    {
        $result = null;
        $changes = $this->uow->getChangeSet($entity);
        $id = $entity->getId();
        if (isset($changes['active'])) {
            $changes['is_active'] = $changes['active'];
            unset($changes['active']);
        }

        $categories = $changes['categories'] ?? [];
        unset($changes['categories']);

        if (!empty($changes)) {
            $result = $this->query()
                ->where('id', $id)
                ->update($changes);
        }

        foreach ($categories as $category) {
            $maps[] = [
                'affiliate_id' => $id,
                'category_id' => $category
            ];
        }

        if (!empty($maps)) {
            $this->query()
                ->newQuery()
                ->table('affiliate_category')
                ->where('affiliate_id', $id)
                ->delete();
            $this->query()
                ->newQuery()
                ->table('affiliate_category')
                ->insert($maps);
        }
        return $result;
    }

    public function findAll(): array
    {
        // Get all affiliates
        $affiliates = $this->query()
            ->nativeQuery()
            ->table($this->table)
            ->addSelect(['id', 'name', 'email', 'url', 'is_active'])
            ->get();

        // Get ids related to categories to retrieve
        $ids = array_map(function (array $affiliate) {
            return $affiliate['id'];
        }, $affiliates);

        // Get all categories once
        $categories = $this->query()
            ->nativeQuery()
            ->select(['affiliate_id', 'category_id'])
            ->table('affiliate_category')
            ->whereIn('affiliate_id', $ids)
            ->get();

        // Now map every affiliate to its categories
        $affiliates = array_map(function (array $affiliate) use ($categories) {
            $id = $affiliate['id'];
            $affiliate['categories'] = array_filter($categories, function (array $pivot) use ($id) {
                return $id === $pivot['affiliate_id'];
            });
            return $affiliate;
        }, $affiliates);

        // Return created entities
        return array_map([$this, 'createEntity'], $affiliates);
    }
}
