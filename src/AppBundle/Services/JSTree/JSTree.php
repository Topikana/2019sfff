<?php

namespace AppBundle\Services\JSTree;

use AppBundle\Services\JSTree\Renderer\HumanRenderer;
use AppBundle\Services\JSTree\Renderer\EmailRenderer;

use \Lavoisier\Query;
use \Lavoisier\Hydrators\IHydrator;
use \Lavoisier\Hydrators\SimpleXMLHydrator;
use \Lavoisier\Hydrators\DefaultHydrator;

class JSTree
{
    private $treeMap;
    private $hostname;

    public function __construct($hostname, $branchesInformation)
    {
        $this->treeMap = $branchesInformation;
        $this->hostname = $hostname;
    }

    /**
     * Creates and populates the Recipient Lists (branches) information according to Lavoisier
     */
    public function populateRecipients($userDN)
    {
        $branches = array();
        foreach($this->treeMap as $branchKey=>$branchInfo)
        {


            $branch = new RecipientCollection($branchKey, $branchInfo['label']);
            try
            {
            $xml_recipients =  $this->getLavoisierView($branchInfo, $userDN, 'recipients_view');
                $branch->populate($xml_recipients);

                if($branchInfo['root_checked_behaviour'] == RecipientCollection::ROOT_AS_MAILING_LIST_CC)
                    $branch->addRootBehaviour($branchInfo['mailing_list'], $branchInfo['root_checked_behaviour']);

                $branches[$branchKey] = $branch;
            }
            catch (\Exception $e)
            {
                echo "Error: ".$e->getMessage();
            }


        }
        $this->recipientList = $branches;
    }

    public function getLavoisierBaseURL(){
        return $this->hostname;
    }


    /**
     * @param null $treeStructure
     * @return array
     *
     * Generates the "html_data" for the jsTree (array with one element per tree branch).
     * If $treeStructure is null, returns a tree with nothing checked (new broadcast).
     * Otherwise, boxes are checked according to the $treeStructure parameter (new broadcast from template).
     *
     * The parameter $userDN is used for the User Contacts branch, but is passed to Lavoisier in all views for consistency.
     */
    public function getBranchesHTML($userDN, $treeStructure = null)
    {
        if(isset($treeStructure))
//            return $this->getBranchesHTMLFromStructure($treeStructure);
            return $treeStructure;
        else
            return $this->getBlankBranchesHTML($userDN);
    }


    /**
     * @return array
     * Returns the "html_data" for the jsTree, with nothing checked.
     * Queries Lavoisier for each branch of the tree and puts the information in the return array.
     *
     * The parameter $userDN is used for the User Contacts branch, but is passed to Lavoisier in all views for consistency.
     */
    private function getBlankBranchesHTML($userDN)
    {
        $branches = array();
        foreach($this->treeMap as $branchInfo)
        {
            if ($branchInfo['enabled']=="true"){

                try
                {
                $res = $this->getLavoisierView($branchInfo, $userDN, 'tree_view');
                if (is_object($res))
                $branches[$branchInfo["lavoisierPrefix"]]=$res->children()->asXML();
                }

                catch (\Exception $e) {
                    echo "Error: ".$e->getMessage();
                }


              //  array_merge($branches,array($branchInfo["lavoisierPrefix"]=>$res->children()->asXML()));
            }
        }

     //   print_r($branches);
        return $branches;
    }

    private function getLavoisierView($branchInfo, $userDN, $view_type) {

        $query = new Query($this->hostname, $branchInfo[$view_type]);


        if($branchInfo['useDN']) {

            $query->setPath($userDN);
        }
        try {

            $query->setHydrator(new SimpleXMLHydrator);
            $result = $query->execute();

            } catch (\Exception $e) {
            $result=NULL;

        }

        return $result;
    }

    /**
     * The jstree from the current template, containing selected targets.
     * @param $tree
     * @return array
     * Composes an array from the tree branches (Recipient Collections), to be displayed on the broadcast confirmation template.
     */
    public function renderHuman($tree)
    {
        $branchRecipients = array();
        foreach($tree as $key=>$branch)
        {

            $renderer = new HumanRenderer($branch, $this->recipientList[$key]);
            $branchRecipients[$this->treeMap[$key]['label']] = $renderer->render();
        }
        return $branchRecipients;
    }

    public function renderHumanAsHTML($tree){

        $recipients = $this->renderHuman($tree);

        $counter = 0; $nb= count($recipients);
        foreach($recipients as $key => $item ) {
            if(!is_string($item)) {
                echo "<b>$key </b> : ";
                self::renderRecipients($item);
            }
            else {
                $separator = ',';
                if($counter  == $nb-1) $separator ='';
                if(is_string($key)) {
                    echo "<b>$key </b> : $item$separator&nbsp;";
                }
                else {
                    echo $item.$separator.'&nbsp;';
                }
            }
            $counter++;
        }

    }

    /**
     * The jstree from the current template, containing selected targets.
     * @param $tree
     * @return array
     * Composes an array from the tree branches (Recipient Collections), to be saved at broadcast_message.targets_mail.
     */
    public function renderEmail($tree)
    {
        $branchRecipients = array();
        foreach($tree as $key=>$branch)
        {


            $renderer = new EmailRenderer($branch, $this->recipientList[$key]);
//            $branchRecipients[$this->treeMap[$key]['label']] = $renderer->render();
            $branchRecipients = array_merge($branchRecipients, $renderer->render());

        }


        return $branchRecipients;
    }

    /**
     * The jstree from the current template, containing selected targets.
     * @param $tree
     * @return array
     * Composes an array from the tree branches (Recipient Collections), to be saved at broadcast_message.targets_id.
     */
    public function renderIDs($tree)
    {
        $arrayIDs = array();
        foreach($this->treeMap as $key=>$branch)
        {
            if(isset($tree[$key]))
            $arrayIDs[$key] = $tree[$key];
        }
        return $arrayIDs;
    }


    /**
     * @param $recipient
     * @return <string>
     * rendering function for the recipients.
     */

    static public function renderRecipients($recipients) {
        $counter = 0; $nb= count($recipients);
        foreach($recipients as $key => $item ) {
            if(!is_string($item)) {
                echo "<b>$key </b> : ";
                self::renderRecipients($item);
            }
            else {
                $separator = ',';
                if($counter  == $nb-1) $separator ='';

                if (!(stristr($item, "ERR:"))) {
                    if (is_string($key)) {
                        echo "<b>$key </b> : $item$separator&nbsp;";
                    } else {
                        echo $item . $separator . '&nbsp;';
                    }
                }
            }
            $counter++;
        }
    }
}
