<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchNewController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Request $request)
    {
        return $this->render('/search/search_new.html.twig', [
            'pagetitle'       => "Card Search (new)",
            'pagedescription' => "New Card Search",
            'query'           => $request->query->get('q') ?: '',
            'sort'            => $request->query->get('sort') ?: '',
            'view'            => $request->query->get('view') ?: '',
        ]);
    }
}
