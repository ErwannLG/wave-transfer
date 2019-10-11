<?php

namespace App\Controller;

use App\Form\TransferType;
use App\Entity\Transfer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class TransferController extends AbstractController
{

  // public function index()
  // {
  //     return $this->render('transfer/index.html.twig', [
  //         'controller_name' => 'TransferController',
  //     ]);
  // }

  //créer un formulaire pour transfert

   /**
    * @Route("/transfer/new")
    */
  public function new(Request $request){
    $transfer = new Transfer();

    $form = $this->createForm(TransferType::class, $transfer);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      /** @var UploadedFile $userFile */
      $userFile = $form['file']->getData();

      // this condition is needed because the 'file' field is not required
      // so the PDF file must be processed only when a file is uploaded
      if ($userFile) {
          $originalFilename = pathinfo($userFile->getClientOriginalName(), PATHINFO_FILENAME);
          // this is needed to safely include the file name as part of the URL
          $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
          $newFilename = $safeFilename.'-'.uniqid().'.'.$userFile->guessExtension();

          // Move the file to the directory where files are stored
          try {
              $userFile->move(
                  $this->getParameter('files_directory'),
                  $newFilename
              );
          } catch (FileException $e) {
              // ... handle exception if something happens during file upload
          }

          // updates the 'userFilename' property to store the PDF file name
          // instead of its contents
          $transfer->setFilename($newFilename);

          // Create the message
          $message = (new Swift_Message())
            // Give the message a subject
            ->setSubject('Your subject')
            // Set the From address with an associative array
            ->setFrom(['john@doe.com' => 'John Doe'])
            // Set the To addresses with an associative array (setTo/setCc/setBcc)
            ->setTo(['other@domain.org' => 'A name'])
            // Give it a body
            ->setBody('Here is the message itself')
            // And optionally an alternative body
            ->addPart('<q>Here is the message itself</q>', 'text/html')
            // Optionally add any attachments
            ->attach(Swift_Attachment::fromPath(asset('uploads/files/' ~ $newFilename))->setFilename($userFile->getClientOriginalName()))
            ;
      }

      // ... persist the $transfer variable or any other work
      return $this->redirect($this->generateUrl('app_transfer_list'));
    }

    return $this->render('transfer/index.html.twig', [
      'form'=>$form->createView(),
      'controller_name' => 'TransferController'
    ]);
  }
}
