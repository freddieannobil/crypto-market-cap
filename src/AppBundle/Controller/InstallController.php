<?php
/**
 * Created by PhpStorm.
 * User: fredd
 * Date: 29/12/2016
 * Time: 22:06
 */

namespace AppBundle\Controller;

use AppBundle\Entity\BtcCrypto;
use AppBundle\Entity\CryptoCurrency;
use AppBundle\Entity\Currency;
use AppBundle\Entity\CurrencyPrice;
use AppBundle\Entity\EthCrypto;
use AppBundle\Entity\Import;
use Doctrine\ORM\Tools\SchemaTool;

use AppBundle\Entity\Page;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;

use AppBundle\Entity\AudCrypto;
use AppBundle\Entity\BrlCrypto;
use AppBundle\Entity\CadCrypto;
use AppBundle\Entity\ChfCrypto;
use AppBundle\Entity\ClpCrypto;
use AppBundle\Entity\CnyCrypto;
use AppBundle\Entity\CzkCrypto;
use AppBundle\Entity\DkkCrypto;
use AppBundle\Entity\EurCrypto;
use AppBundle\Entity\GbpCrypto;
use AppBundle\Entity\HkdCrypto;
use AppBundle\Entity\HufCrypto;
use AppBundle\Entity\IdrCrypto;
use AppBundle\Entity\ILsCrypto;
use AppBundle\Entity\InrCrypto;
use AppBundle\Entity\JpyCrypto;
use AppBundle\Entity\KrwCrypto;
use AppBundle\Entity\MxnCrypto;
use AppBundle\Entity\MyrCrypto;
use AppBundle\Entity\NokCrypto;
use AppBundle\Entity\NzdCrypto;
use AppBundle\Entity\PhpCrypto;
use AppBundle\Entity\PkrCrypto;
use AppBundle\Entity\PlnCrypto;
use AppBundle\Entity\RubCrypto;
use AppBundle\Entity\SekCrypto;
use AppBundle\Entity\SgdCrypto;
use AppBundle\Entity\ThbCrypto;
use AppBundle\Entity\TryCrypto;
use AppBundle\Entity\TwdCrypto;
use AppBundle\Entity\UsdCrypto;
use AppBundle\Entity\ZarCrypto;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;


class InstallController extends Controller
{
    /**
     * @Route("/install", name="install")
     */
    public function installAction(Request $request)
    {

        try{

            $form = $this->createForm('AppBundle\Form\Install\AddDatabaseType');
            $form->handleRequest($request);

            $admin_form = $this->createForm('AppBundle\Form\Install\AddAdminType');
            $admin_form->handleRequest($request);

            $remove_form = $this->createForm('AppBundle\Form\Install\RemoveFileType');
            $remove_form->handleRequest($request);

            //db
            if ($form->isSubmitted() && $form->isValid()) {

                if($request->request->get('db')){
                    $em = $this->getDoctrine()->getManager();
                    $tool = new SchemaTool($em);
                    $classes = array(
						$em->getClassMetadata(User::class),
						$em->getClassMetadata(Currency::class),
						$em->getClassMetadata(CryptoCurrency::class),
						$em->getClassMetadata(Page::class),

                    );
                    $tool->createSchema($classes);

					$request->getSession()
                        ->getFlashBag()
                        ->add('db_success', 'Installed database successfully');

                    return $this->redirectToRoute('install');
                }
            }

            //admin
            if ($admin_form->isSubmitted() && $admin_form->isValid()) {

                if($request->request->get('admin')){
                    $user = new User();
                    $user->setEmail('admin@admin.com');
                    $user->setPlainPassword('admin');
                    $user->setUsername('admin');
                    $user->setFirstName('Webber');
                    $user->setLastName('doo');
                    $user->setRoles(['ROLE_ADMIN']);
                    $user->setEnabled(true);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);

					$array =["USD"];

					foreach($array as $value){
						$currency = new Currency();
						$currency->setName($value);
						$em->persist($currency);
					}
					$em->flush();
					$em->clear();

                    $request->getSession()
                        ->getFlashBag()
                        ->add('admin_success', 'Installed admin details successfully');

                    return $this->redirectToRoute('install');
                }

            }

            //remove file
            if ($remove_form->isSubmitted() && $remove_form->isValid()) {

                if ($request->request->get('remove')) {
                    try {

                        unlink('src/AppBundle/Controller/InstallController.php');
                        unlink('src/AppBundle/Controller/UpdateController.php');


                        $request->getSession()
                            ->getFlashBag()
                            ->add('remove_success', 'Installer file has been deleted.');

                        return $this->redirectToRoute('homepage');


                    } catch (\Exception $e) {
                        return $this->redirectToRoute('homepage');
                    }
                }

            }


        }catch (Exception $e){
            echo 'There was a problem with the installation: Error - '. $e->getMessage();
        }

        return $this->render($this->getParameter('theme_name').'/install/index.html.twig',[
            'db_form' => $form->createView(),
            'admin_form' => $admin_form->createView(),
            'remove_form' => $remove_form->createView(),
        ]);
    }

}