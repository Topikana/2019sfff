<?php
namespace AppBundle\Controller\Backend\LogAccess;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/a/backend/logsAccess")
 */
class logsAccessController extends Controller
{
    private function readingDir($server)
    {
          switch ($server) {
            case "apache":
               $directoLogs = $this->container->getParameter("dirLogsApache");
               break;
          case "symfony3":
              $directoLogs = $this->container->getParameter("dirLogsSymfony3");
              break;
          case "symfony1":
               $directoLogs = $this->container->getParameter("dirLogsSymfony1");
               break;
            default:
                $directoLogs ="";
                break;

            }

    if (!empty($directoLogs)) {
        $listlog = scandir($directoLogs);

    }else{
        $listlog="DEAD";
    }
    return $listlog;

    }


    /**
     * @Route("/tableView/{server}", name="tableView")
     *SHow the differents files in a log directory
     */
    public function tableViewAction($server)
    {
        $listlog=$this->readingDir($server);

        if (is_array($listlog)) {
            $tableFile = array_slice($listlog, 2);
            $file = array_values($tableFile);

            return $this->render(':backend/LogAccess:tableLogs.html.twig', array('pageTitle' => 'Accurate logs', 'file'=>$file,'tablefile'=>$tableFile,'server'=>$server));
        }else{
            return $this->render(':backend/LogAccess:tableLogs.html.twig', array('pageTitle' => 'Accurate logs'));
        }


    }



    /**
     * @Route("/tableView/{server}/{file}", name="viewRegexpTable")
     * Selection of different available regular expression for a chosen file
     */
    public function viewRegexpTableAction($server,$file)
    {
        $listlog = $this->readingDir($server);
        $prelude = explode(".",$file);

        if (in_array($prelude[0] , ["access", "error","php_errors", "ssl_request"])) {
            $regexp=$prelude[0];
            return $this->render(":backend/LogAccess:regexpTab.html.twig", array('pageTitle' => $file,'regexp_choice'=>$regexp, 'server'=>$server, 'file'=>$file));
        }elseif (strstr($prelude[0] , "dev") or strstr($prelude[0] ,  "prod" )) {
            $regexp="sf3";
            return $this->render(":backend/LogAccess:regexpTab.html.twig", array('pageTitle' => $file,'regexp_choice'=>$regexp, 'server'=>$server, 'file'=>$file));
        }elseif (strstr($prelude[0] , "frontend")) {
            $regexp="sf1";
            return $this->render(":backend/LogAccess:regexpTab.html.twig", array('pageTitle' => $file,'regexp_choice'=>$regexp, 'server'=>$server, 'file'=>$file));
        }else{
            return $this->render(":backend/LogAccess:regexpTab.html.twig", array('pageTitle' => $file,'server'=>$server, 'file'=>$file));
        }


    }


    /**
     * @Route("/showLogTableAjax/{server}/{file}", name="showLogTableAjax")
     * When a regexp is chosen, this part is showing the result
     *
     */
    public function showLogTableAjaxAction(Request $request, $server, $file) {

        $prelude=explode(".",$file);

        if (in_array($prelude[0] , ["access", "error","php_errors", "ssl_request" ])) {
            $regexp=$prelude[0];
        }elseif (strstr($prelude[0] , "dev") or strstr($prelude[0] ,  "prod" )) {
            $regexp="sf3";
        }elseif (strstr($prelude[0] , "frontend")) {
            $regexp="sf1";
        }else{
            return $this->render(":backend/LogAccess:regexpTab.html.twig", array('pageTitle' => $file,'server'=>$server, 'file'=>$file));
        }

        $chosenRegexp = $request->get('regexp');

        $listlog=$this->readingDir($server);

        switch($server) {
            case "apache":
                $directoLogs = $this->container->getParameter("dirLogsApache");
                break;
            case "symfony3":
                $directoLogs = $this->container->getParameter("dirLogsSymfony3");
                break;
            case "symfony1":
                $directoLogs = $this->container->getParameter("dirLogsSymfony1");
                break;
        }

        $a=array();
        $detail=array();

        if (in_array($file, $listlog)) {
            $path = $directoLogs . "/" . $file;
            $log = file($path);

            $pos = array();
            $u=0;

            for ($i = 0; $i < count($log); $i++) {
                    preg_match($chosenRegexp, $log[$i], $pos[$i]);
                }
            }


            //Case in regexp for access and error
            //Number of column with the regex
            $nbcol = 0;

                    for ($i = 0; $i <= count($pos); $i++) {
                        if (!empty($pos[$i])) {
                            $nbcol = count($pos[$i]);
                        }
                    }

                    $o = 0;

                    for ($i = 0; $i <= count($pos); $i++) {
                        if (isset($pos[$i][0])) {
                            $detail[$i] = $pos[$i][0];
                        }

                        if ($nbcol <= 7) {
                            for ($j = 1; $j <= count($pos); $j++) {
                                if (isset($pos[$i][$j])) {
                                    $a[$i][$o] = $pos[$i][$j];
                                    $o++;
                                }
                            }

                        } else {
                            if (!empty($pos[$i])) {
                                $a[$i][0] = $pos[$i][1];
                                $a[$i][1] = $pos[$i][4];
                                $a[$i][2] = $pos[$i][5];
                                $a[$i][3] = $pos[$i][6];
                                $a[$i][4] = $pos[$i][7];
                                $a[$i][5] = $pos[$i][8];
                                $a[$i][6] = $pos[$i][10];
                                $a[$i][7] = $pos[$i][13];
                            }
                        }
                    }


            $matches = array_values($a);

            return $this->render(":backend/LogAccess/template:logTableWithRegexp.html.twig",
                array("chosenRegexp" => $chosenRegexp, 'regexp_choice' => $regexp, 'pageTitle' => $file, 'log' => $a, 'ln' => $matches, 'cool' => $detail, 'server' => $server));
        }
    //}

}
?>

