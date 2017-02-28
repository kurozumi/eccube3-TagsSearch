<?php

/*
 * This file is part of the TagsSearch
 *
 * Copyright (C) 2017 タグ検索プラグイン
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\TagsSearch;

use Eccube\Application;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

class TagsSearchEvent
{

    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function onFrontProductIndexInitialize(EventArgs $event)
    {
        $builder = $event->getArgument('builder');
        $builder->add('tag_ids', 'entity', array(
            'class' => 'Eccube\Entity\ProductTag',
            'empty_value' => '全ての商品タグ',
            'empty_data' => null,
            'required' => false,
            'multiple' => true
        ));
    }

    public function onFrontProductIndexSearch(EventArgs $event)
    {
        $searchData = $event->getArgument('searchData');
        $qb = $event->getArgument('qb');
        
            print_r($searchData);
            exit;

        // category
        if (!empty($searchData['tag_ids']) && $searchData['tag_ids']) {

            $Tags = array();
            foreach ($searchData['tag_ids'] as $Tag) {
                $Tags[] = $Tag->getId();
            }
            
            if ($Tags) {
                $qb
                        ->innerJoin('p.ProductTag', 'ptt')
                        ->innerJoin('ptt.ProductTag', 't')
                        ->andWhere($qb->expr()->in('ptt.ProductTag', ':ProductTag'))
                        ->setParameter('ProductTag', $Tags);
            }
        }
    }

}
