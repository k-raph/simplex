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
     * @return object
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

        $categories = $this->query()
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
     * @param Affiliate $affiliate
     * @return mixed|void
     */
    public function insert(IdentifiableInterface $affiliate)
    {
        $insert = $this->extract($affiliate);

        $this->database->transaction(function () use ($insert, $affiliate) {
            $id = $this->query()->insertGetId($insert);
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
     * @param Affiliate $entity
     * @return array
     */
    public function extract(IdentifiableInterface $affiliate): array
    {
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

            return $result;
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
    }
}