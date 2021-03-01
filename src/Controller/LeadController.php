<?php

namespace App\Controller;

use App\Entity\Lead;
use App\Form\LeadType;
use App\Repository\LeadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LeadController extends AbstractController
{
    private $leadRepo;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepo = $leadRepository;
    }

    /**
     * @Route("/lead", name="lead")
     */
    public function index(): Response
    {
        $leads = $this->leadRepo->findBy(['company' => $this->getUser()->getCompany()]);
        return $this->render('lead/index.html.twig', [
            'leads' => $leads,
        ]);
    }
    /**
     * @Route("/lead/create", name="create-lead")
     */
    public function create(Request $request)
    {
        $lead = new Lead();
        return $this->form($lead,$request);
    }
    /**
     * @Route("/lead/update/{id}", name="update-lead")
     */
    public function update($id,Request $request)
    {
        $lead = $this->leadRepo->find($id);
        return $this->form($lead,$request);
    }
    /**
     * @Route("/lead/remove/{id}", name="remove-lead")
     */
    public function remove($id)
    {
        $lead = $this->leadRepo->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($lead);
        $em->flush();

        return $this->redirect($this->generateUrl('lead'));
    }
    /**
     * @Route("/lead/show/{id}", name="show-lead")
     */
    public function show($id)
    {
        $lead = $this->leadRepo->find($id);
        return $this->render('lead/show.html.twig',[
            'lead' => $lead
        ]);
    }

    public function form($lead,$request)
    {
        $form = $this->createForm(LeadType::class,$lead);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            //$user = $this->getUser();
            $company = $this->getUser()->getCompany();
            $em = $this->getDoctrine()->getManager();
            //$lead->setUserClientId($user);
            $lead->setCompany($company);
            $em->persist($lead);
            $em->flush();

            return $this->redirect($this->generateUrl('lead'));
        }
        return $this->render('lead/create.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
