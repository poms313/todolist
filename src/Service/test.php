<?php

namespace App\Service;

use Symfony\Component\Routing\Annotation\Route;


class test
{
    /**
     * @Route("/testj", name="testj")
     */
    public function test()
    {
        header('Content-Type: application/json');
        /*       $w = json_encode([
                   [
                    'title' => 'Lorem Ipsum',
                    'start' =>  '2019-11-16T13:00:00',
                    'end' =>  '2019-11-17T14:00:00'
                   ],
                   [
                    'title' => 'The Test',
                    'start' =>  '2019-11-16T10:00:00',
                    'end' =>  '2019-11-17T13:00:00'
                   ]
                ]);
                echo $w;*/
        //    $obbbbbbb = json_decode($_GET["x"], false);
        $repository = $this->getDoctrine()->getRepository(UserTask::class);
        $ts = $repository->findAll();
        $b = array();

        foreach ($ts as $t) {
            $object = new \stdClass();
            $object->title = $t->getTaskName();
            $object->start = $t->getTaskStartDate()->format('Y-m-d H:i');
            $object->end = $t->getTaskEndDate()->format('Y-m-d H:i');
            //    $object->allDay=true;



            //     var_dump($myJSON);
            //  var_dump($object);
            array_push($b, $object);
        }
        echo json_encode($b);
        $pp = json_encode($b);
        var_dump($b);
        // }
        /*       return $this->render('test/index.html.twig', [
            //   'myJSON' => $myJSON,
             //  'w' => $w,
               
           ]);*/
    }
}
