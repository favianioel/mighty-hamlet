<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixture extends BaseFixture
{
    private $dramaTags;

    public function __construct()
    {
        $this->dramaTags =[
            'moarte', 'viata', 'razboi', 'dragoste', 'ingeri',
            'demoni', 'lucifer', 'Dumnezeu', 'cautare', 'drama'
        ];
    }

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(10, 'main_tags', function($i) {
            $tag = new Tag();
            $tag->setName($this->dramaTags[$i]);
            return $tag;
        });

        $manager->flush();
    }
}
