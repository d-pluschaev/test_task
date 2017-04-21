<?php

namespace AdminBundle\Controller;

use AdminBundle\Repository\UsersRepository;
use AdminBundle\Service\CSVImporters\UserCSVFileImporter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/admin/")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }

    /**
     * @Route("/admin/import/csv_upload")
     * @Method("POST")
     */
    public function actionImportCsv(Request $request)
    {
        ini_set('max_execution_time', 300);

        $response = [
            'error' => true,
            'message' => ''
        ];

        $csvFilePath = null;
        foreach ($request->files as $uploadedFile) {
            $csvFilePath = $uploadedFile->getPathName();
            break;
        }

        if ($csvFilePath) {
            /** @var UserCSVFileImporter $csvFileImporter */
            $csvFileImporter = $this->get('admin.csv_file_importer.users');
            try {
                $time = time();
                $lineNumbers = $csvFileImporter->processFile($csvFilePath);
                $response['error'] = false;
                $response['message'] = "Import done, $lineNumbers lines imported, took: "
                    . (time() - $time) . " seconds";
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['message'] = "There is no file to import. It may be caused by POST size limit in php.ini: "
                .ini_get('post_max_size');
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/search")
     * @Method("POST")
     */
    public function actionSearch(Request $request)
    {
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->getDoctrine()->getRepository("AdminBundle:Users");

        $onPage = $request->get("on_page", 10);
        $page = $request->get("page", 1);
        $page = $page < 1 ? 1 : $page;
        $offset = ($page -1) * $onPage;

        return new JsonResponse(
            $usersRepository->processSearch($request->request->all(), $offset)
        );
    }

    /**
     * @Route("/admin/searchTest/")
     */
    public function actionSearchTest(Request $request)
    {
        /** @var UsersRepository $usersRepository */
        $usersRepository = $this->getDoctrine()->getRepository("AdminBundle:Users");

        $onPage = $request->get("on_page", 10);
        $page = $request->get("page", 1);
        $page = $page < 1 ? 1 : $page;
        $offset = ($page -1) * $onPage;

        return new JsonResponse(
            $usersRepository->processSearch($request->query->all(), $offset)
        );
    }
}
