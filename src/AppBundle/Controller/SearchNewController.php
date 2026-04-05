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
        return $this->render('/Search/search_new.html.twig', [
            'pagedescription' => "New Card Search",
            'pagetitle'       => "Card Search (new)",
            'format'          => $request->query->get('format') ?: '',
            'printing_type'   => $request->query->get('printing_type') ?: '',
            'query'           => $request->query->get('q') ?: '',
            'sort'            => $request->query->get('sort') ?: '',
            'view'            => $request->query->get('view') ?: '',
        ]);
    }
}
