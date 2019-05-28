<?php

namespace AppBundle\Tests\Entity\VO;

use AppBundle\Entity\VO\Disciplines;
use AppBundle\Entity\VO\Vo;
use AppBundle\Entity\VO\VoAcknowledgmentStatements;
use AppBundle\Entity\VO\VoContactHasProfile;
use AppBundle\Entity\VO\VoContacts;
use AppBundle\Entity\VO\VoDiscipline;
use AppBundle\Entity\VO\VoDisciplines;
use AppBundle\Entity\VO\VoField;
use AppBundle\Entity\VO\VoHeader;
use AppBundle\Entity\VO\VoMailingList;
use AppBundle\Entity\VO\VoMetrics;
use AppBundle\Entity\VO\VoRegexp;
use AppBundle\Entity\VO\VoReport;
use AppBundle\Entity\VO\VoRessources;
use AppBundle\Entity\VO\VoRobotCertificate;
use AppBundle\Entity\VO\VoScope;
use AppBundle\Entity\VO\VoStatus;
use AppBundle\Entity\VO\VoTests;
use AppBundle\Entity\VO\VoUserProfile;
use AppBundle\Entity\VO\VoUsers;
use AppBundle\Entity\VO\VoUsersHistory;
use AppBundle\Entity\VO\VoUsersMetrics;
use AppBundle\Entity\VO\VoUsersMetricsbyCA;
use AppBundle\Entity\VO\VoVomsGroup;
use AppBundle\Entity\VO\VoVomsServer;
use AppBundle\Entity\VO\VoYearlyValidation;

class Populate extends \PHPUnit\Framework\TestCase
{

    /**
     * @return Vo
     */
    public static function createVo(){

        $data = Populate::datasource();


        // Set Vo Data
        $vo = new Vo();
        $vo->setName($data["vo.name"]);
        $vo->setValidationDate($data["vo.validation_date"]);
        $vo->setCreationDate($data["vo.creation_date"]);
        $vo->setLastChange($data["vo.last_change"]);
        $vo->setHeaderId($data["vo.header_id"]);
        $vo->setRessourcesId($data["vo.ressources_id"]);
        $vo->setMailingListId($data["vo.mailing_list_id"]);
        $vo->setGgusTicketId($data["vo.ggus_ticket_id"]);
        $vo->setNeedVomsHelp($data["vo.need_voms_help"]);
        $vo->setNeedGgusSupport($data["vo.need_ggus_support"]);
        $vo->setVomsTicketId($data["vo.voms_ticket_id"]);
        $vo->setGgusTicketIdSuCreation($data["vo.ggus_ticket_id_su_creation"]);
        $vo->setMonitored($data["vo.monitored"]);
        $vo->setEnableTeamTicket($data["vo.enable_team_ticket"]);

        return $vo;

    }

    /**
     * @return Vo
     */
    public static function createVoAcknowledgmentStatements(){

        $data = Populate::datasource();


        // Set Vo Acknowledgment Statements
        $voAS = new VoAcknowledgmentStatements();
        $voAS->setSerial($data["as.serial"]);
        $voAS->setTypeAs($data["as.type_as"]);
        $voAS->setGrantid($data["as.grantid"]);
        $voAS->setSuggested($data["as.suggested"]);
        $voAS->setRelationShip($data["as.relationShip"]);
        $voAS->setPublicationUrl($data["as.publicationUrl"]);

        return $voAS;

    }

    /**
     * @return Disciplines
     */
    public static function createDisciplines(){

        $data = Populate::datasource();

        // Set disciplines
        $discipline = new Disciplines();
        $discipline->setDisciplineId($data["disciplines.id"]);
        $discipline->setDisciplineLabel($data["disciplines.label"]);

        return $discipline;

    }

    /**
     * @return VoContactHasProfile
     */
    public static function createVoContactHasProfile(){

        $data = Populate::datasource();

        // Set Vo Contact Has Profile
        $discipline = new VoContactHasProfile();
        $discipline->setSerial($data["chp.serial"]);
        $discipline->setUserProfileId($data["chp.user_profile_id"]);
        $discipline->setContactId($data["chp.contact_id"]);
        $discipline->setComment($data["chp.comment"]);

        return $discipline;

    }

    /**
     * @return VoContacts
     */
    public static function createVoContacts(){

        $data = Populate::datasource();

        // Set Vo Contacts
        $discipline = new VoContacts();
        $discipline->setFirstName($data["c.first_name"]);
        $discipline->setLastName($data["c.last_name"]);
        $discipline->setDn($data["c.dn"]);
        $discipline->setEmail($data["c.email"]);
        $discipline->setGridBody($data["c.grid_body"]);

        return $discipline;

    }

    /**
     * @return VoDiscipline
     */
    public static function createVoDiscipline(){

        $data = Populate::datasource();

        // Set Vo Contacts
        $discipline = new VoDiscipline();

        $discipline->setDiscipline($data["d.discipline"]);
        $discipline->setDescription($data["d.description"]);

        return $discipline;

    }


    /**
     * @return VoDisciplines
     */
    public static function createVoDisciplines(){

        $data = Populate::datasource();

        // Set Vo Contacts
        $disciplines = new VoDisciplines();

        $disciplines->setDisciplineId($data["ds.discipline_id"]);
        $disciplines->setVoId($data["ds.vo_id"]);

        return $disciplines;

    }

    /**
     * @return VoField
     */
    public static function createVoField(){

        $data = Populate::datasource();

        // Set disciplines
        $field = new VoField();
        $field->setFieldName($data["f.field_name"]);
        $field->setFieldErrorMsg($data["f.field_error_msg"]);
        $field->setFieldUserHelp($data["f.field_user_help"]);
        $field->setFieldAdminHelp($data["f.field_admin_help"]);
        $field->setCategory($data["f.category"]);
        $field->setFieldLink($data["f.field_link"]);
        $field->setFieldExample($data["f.field_example"]);
        $field->setFieldRegexpId($data["f.field_regexp_id"]);
        $field->setRequired($data["f.required"]);

        return $field;

    }

    /**
     * @return VoReport
     */
    public static function createVoReport(){

        $data = Populate::datasource();

        $report = new VoReport();
        $report->setReportBody($data["r.report_body"]);
        $report->setSerial($data["r.serial"]);
        $report->setCreatedAt($data["r.created_at"]);
        $report->setUpdatedAt($data["r.updated_at"]);

        return $report;
    }

    /**
     * @return voHeader
     */
    public static function createVoHeader(){

        $data = Populate::datasource();

        // Set disciplines
        $voHeader = new VoHeader();
        $voHeader->setName($data["vh.name"]);
        $voHeader->setAlias($data["vh.alias"]);
        $voHeader->setGridId($data["vh.grid_id"]);
        $voHeader->setSerial($data["vh.serial"]);
        $voHeader->setEnrollmentUrl($data["vh.enrollment_url"]);
        $voHeader->setHomepageUrl($data["vh.homepage_url"]);
        $voHeader->setSupportProcedureUrl($data["vh.support_procedure_url"]);
        $voHeader->setAup($data["vh.aup"]);
        $voHeader->setAupType($data["vh.aup_type"]);
        $voHeader->setDescription($data["vh.description"]);
        $voHeader->setArcSupported($data["vh.arc_supported"]);
        $voHeader->setGliteSupported($data["vh.glite_supported"]);
        $voHeader->setUnicoreSupported($data["vh.unicore_supported"]);
        $voHeader->setGlobusSupported($data["vh.globus_supported"]);
        $voHeader->setCloudComputingSupported($data["vh.cloud_computing_supported"]);
        $voHeader->setCloudStorageSupported($data["vh.cloud_storage_supported"]);
        $voHeader->setDesktopGridSupported($data["vh.desktop_grid_supported"]);
        $voHeader->setValidationDate($data["vh.validation_date"]);
        $voHeader->setDisciplineId($data["vh.discipline_id"]);
        $voHeader->setScopeId($data["vh.scope_id"]);
        $voHeader->setStatusId($data["vh.status_id"]);
        $voHeader->setInsertDate($data["vh.insert_date"]);
        $voHeader->setUser($data["vh.user"]);
        $voHeader->setValidated($data["vh.validated"]);
        $voHeader->setRejectReason($data["vh.reject_reason"]);
        $voHeader->setNotifySites($data["vh.notify_sites"]);

        return $voHeader;

    }

    /**
     * @return VoMailingList
     */
    public static function createVoMailingList(){

        $data = Populate::datasource();

        // Set mailing list
        $voHeader = new VoMailingList();
        $voHeader->setAdminsMailingList($data["ml.admins_mailing_list"]);
        $voHeader->setOperationsMailingList($data["ml.operations_mailing_list"]);
        $voHeader->setUserSupportMailingList($data["ml.user_support_mailing_list"]);
        $voHeader->setUsersMailingList($data["ml.users_mailing_list"]);
        $voHeader->setSecurityContactMailingList($data["ml.security_contact_mailing_list"]);
        $voHeader->setUser($data["ml.user"]);
        $voHeader->setInsertDate($data["ml.insert_date"]);
        $voHeader->setSerial($data["ml.serial"]);
        $voHeader->setValidated($data["ml.validated"]);
        $voHeader->setRejectReason($data["ml.reject_reason"]);
        $voHeader->setNotifySites($data["ml.notify_sites"]);

        return $voHeader;

    }

    /**
     * @return VoMetrics
     */
    public static function createVoMetrics(){

        $data = Populate::datasource();

        // Set disciplines
        $voMetrics = new VoMetrics();
        $voMetrics->setNbVo($data["m.nb_vo"]);
        $voMetrics->setNbAdded($data["m.nb_added"]);
        $voMetrics->setNbRemoved($data["m.nb_removed"]);
        $voMetrics->setNbInterVo($data["m.nb_inter_vo"]);
        $voMetrics->setNbInterAdded($data["m.nb_inter_added"]);
        $voMetrics->setNbInterRemoved($data["m.nb__inter_removed"]);
        $voMetrics->setDayDate($data["m.day_date"]);

        return $voMetrics;

    }

    /**
     * @return VoRegexp
     */
    public static function createVoRegexp(){

        $data = Populate::datasource();

        // Set disciplines
        $voRegexp = new VoRegexp();
        $voRegexp->setDescription($data["rxp.description"]);
        $voRegexp->setRegexpression($data["rxp.regexpression"]);

        return $voRegexp;

    }

    /**
     * @return VoRessources
     */
    public static function createVoRessources(){

        $data = Populate::datasource();

        // Set ressources
        $voRessources = new VoRessources();
        $voRessources->setSerial($data["r.serial"]);
        $voRessources->setInsertDate($data["r.insert_date"]);
        $voRessources->setRam386($data["r.ram386"]);
        $voRessources->setRam64($data["r.ram64"]);
        $voRessources->setJobScratchSpace($data["r.job_scratch_space"]);
        $voRessources->setJobMaxCpu($data["r.job_max_cpu"]);
        $voRessources->setJobMaxWall($data["r.job_max_wall"]);
        $voRessources->setOtherRequirements($data["r.other_requirements"]);
        $voRessources->setCpuCore($data["r.cpu_core"]);
        $voRessources->setVmRam($data["r.vm_ram"]);
        $voRessources->setStorageSize($data["r.storage_size"]);
        $voRessources->setPublicIp($data["r.public_ip"]);
        $voRessources->setUser($data["r.user"]);
        $voRessources->setValidated($data["r.validated"]);
        $voRessources->setRejectReason($data["r.reject_reason"]);
        $voRessources->setNotifySites($data["r.notify_sites"]);
        $voRessources->setCvmfs($data["r.cvmfs"]);

        return $voRessources;

    }

    /**
     * @return VoRobotCertificate
     */
//    public static function createVoRobotCertificate(){
//
//        $data = Populate::datasource();
//
//        // Set robot certificate
//        $voRessources = new VoRobotCertificate();
//        $voRessources->setRobotCertificateId($data["rc.robot_certificate_id"]);
//        $voRessources->setSerial($data["rc.serial"]);
//        $voRessources->setServiceName($data["rc.service_name"]);
//        $voRessources->setServiceUrl($data["rc.service_url"]);
//        $voRessources->setEmail($data["rc.email"]);
//        $voRessources->setRobotDn($data["rc.robot_dn"]);
//        $voRessources->setUseSubProxies($data["rc.use_sub_proxies"]);
//
//        return $voRessources;
//
//    }

    /**
     * @return VoScope
     */
    public static function createVoScope(){

        $data = Populate::datasource();

        // Set Vo Scope
        $voScope = new VoScope();
        $voScope->setScope($data["s.scope"]);
        $voScope->setRoc($data["s.roc"]);
        $voScope->setDecommissioned($data["s.decommissioned"]);

        return $voScope;

    }

    /**
     * @return VoStatus
     */
    public static function createVoStatus(){

        $data = Populate::datasource();

        // Set robot certificate
        $voStatus = new VoStatus();
        $voStatus->setStatus($data["status.status"]);
        $voStatus->setDescription($data["status.description"]);

        return $voStatus;

    }

    /**
     * @return VoTests
     */
    public static function createVoTests(){

        $data = Populate::datasource();

        // Set robot certificate
        $voStatus = new VoTests();
        $voStatus->setRocName($data["t.roc_name"]);
        $voStatus->setTestName($data["t.test_name"]);

        return $voStatus;

    }

    /**
     * @return VoUserProfile
     */
    public static function createVoUserProfile(){

        $data = Populate::datasource();

        // Set robot certificate
        $voUserProfile = new VoUserProfile();
        $voUserProfile->setProfile($data["up.profile"]);
        $voUserProfile->setDescription($data["up.description"]);
        $voUserProfile->setHelpMsg($data["up.help_msg"]);

        return $voUserProfile;

    }

    /**
     * @return VoUsers
     */
    public static function createVoUsers(){

        $data = Populate::datasource();

        // Set robot certificate
        $voUser = new VoUsers();
        $voUser->setDn($data["u.dn"]);
        $voUser->setVo($data["u.vo"]);
        $voUser->setUservo($data["u.uservo"]);
        $voUser->setCa($data["u.ca"]);
        $voUser->setUrlvo($data["u.urlvo"]);
        $voUser->setLastUpdate($data["u.last_update"]);
        $voUser->setFirstUpdate($data["u.first_update"]);
        $voUser->setEmail($data["u.email"]);

        return $voUser;

    }

    /**
     * @return VoUsersHistory
     */
    public static function createVoUsersHistory(){

        $data = Populate::datasource();

        // Set robot certificate
        $voUserHistory = new VoUsersHistory();
        $voUserHistory->setVo($data["uh.vo"]);
        $voUserHistory->setUMonth($data["uh.u_month"]);
        $voUserHistory->setUYear($data["uh.u_year"]);
        $voUserHistory->setNbtotal($data["uh.nbtotal"]);
        $voUserHistory->setNbremoved($data["uh.nbremoved"]);
        $voUserHistory->setNbadded($data["uh.nbadded"]);

        return $voUserHistory;

    }


    /**
     * @return VoUsersMetrics
     */
    public static function createVoUsersmetrics(){

        $data = Populate::datasource();

        // Set robot certificate
        $voUserMetrics = new VoUsersMetrics();
        $voUserMetrics->setVo($data["um.vo"]);
        $voUserMetrics->setDiscipline($data["um.discipline"]);
        $voUserMetrics->setDayDate($data["um.day_date"]);
        $voUserMetrics->setNbtotal($data["um.nbtotal"]);

        return $voUserMetrics;

    }

    /**
     * @return VoUsersMetricsbyCA
     */
    public static function createVoUsersmetricsCA(){

        $data = Populate::datasource();

        // Set robot certificate
        $voUserMetricsCA = new VoUsersMetricsbyCA();
        $voUserMetricsCA->setCa($data["umca.ca"]);
        $voUserMetricsCA->setDayDate($data["umca.day_date"]);
        $voUserMetricsCA->setNbtotal($data["umca.nbtotal"]);

        return $voUserMetricsCA;

    }


    /**
     * @return VoVomsGroup
     */
    public static function createVoVomsGroup(){

        $data = Populate::datasource();

        // Set robot certificate
        $voVomsGroup = new VoVomsGroup();
        $voVomsGroup->setGroupRole($data["vg.group_role"]);
        $voVomsGroup->setDescription($data["vg.description"]);
        $voVomsGroup->setIsGroupUsed($data["vg.is_group_used"]);
        $voVomsGroup->setGroupType($data["vg.group_type"]);
        $voVomsGroup->setAllocatedRessources($data["vg.allocated_ressources"]);
        $voVomsGroup->setSerial($data["vg.serial"]);

        return $voVomsGroup;

    }

    /**
     * @return VoVomsServer
     */
    public static function createVoVomsServer(){

        $data = Populate::datasource();

        // Set robot certificate
        $voVomsServer = new VoVomsServer();
        $voVomsServer->setSerial($data["vs.serial"]);
        $voVomsServer->setHostname($data["vs.hostname"]);
        $voVomsServer->setHttpsPort($data["vs.https_port"]);
        $voVomsServer->setVomsesPort($data["vs.vomses_port"]);
        $voVomsServer->setIsVomsadminServer($data["vs.is_vomsadmin_server"]);
        $voVomsServer->setMembersListUrl($data["vs.members_list_url"]);

        return $voVomsServer;

    }

    /**
     * @return VoYearlyValidation
     */
    public static function createVoYearlyValidation(){

        $data = Populate::datasource();

        // Set robot certificate
        $voYearlyValidation = new VoYearlyValidation();
        $voYearlyValidation->setSerial($data["vy.serial"]);
        $voYearlyValidation->setVoValidation();
        $voYearlyValidation->setDateLastEmailSending($data["vy.date_last_email_sending"]);

        return $voYearlyValidation;

    }

    /**
     * data source for populate
     * @return array
     */
    public static function datasource(){

        $data = [];

        // Set vo data
        $data["vo.serial"] = 1;
        $data["vo.name"] = "aegis";
        $data["vo.validation_date"] = new \DateTime("2011-05-30 10:24:30");
        $data["vo.creation_date"] = new \DateTime("2007-06-07 00:00:00");
        $data["vo.last_change"] = new \DateTime("2015-10-27 15:17:13");
        $data["vo.header_id"] = 299;
        $data["vo.ressources_id"] = 399;
        $data["vo.mailing_list_id"] = 11;
        $data["vo.ggus_ticket_id"] = 0;
        $data["vo.need_voms_help"] = 0;
        $data["vo.need_ggus_support"] = 1;
        $data["vo.voms_ticket_id"] = 0;
        $data["vo.ggus_ticket_id_su_creation"] = 0;
        $data["vo.monitored"] = 0;
        $data["vo.enable_team_ticket"] = 0;

        // Set Vo Acknowledgment Statements data
        $data["as.serial"] = 1;
        $data["as.id"] = 14;
        $data["as.type_as"] = "VO";
        $data["as.grantid"] = 1135;
        $data["as.suggested"] = "Suggested Acknowledgment";
        $data["as.relationShip"] = "using EGI, related to VO:Aegis";
        $data["as.publicationUrl"] = "https://publicationUrl.fr";

        // Set Discipline data
        $data["disciplines.id"] = 1;
        $data["disciplines.label"] = "Other";

        // Set User Has Profile data
        $data["chp.serial"] = 1;
        $data["chp.user_profile_id"] = 1;
        $data["chp.contact_id"] = 1;
        $data["chp.comment"] = "Vo Manager";

        // Set Contacts data
        $data["c.id"] = 1;
        $data["c.first_name"] = "Cyril";
        $data["c.last_name"] = "Lorphelin";
        $data["c.dn"] = "/O=GRID-FR/C=FR/O=CNRS/OU=CC-IN2P3/CN=Cyril Lorphelin";
        $data["c.email"] = "cic-information@in2p3.fr";
        $data["c.grid_body"] = 1;

        // Set Discipline data
        $data["d.id"] = 1;
        $data["d.discipline"] = "Others";
        $data["d.description"] = "Others description";

        // Set List Vo Disciplines data
        $data["ds.discipline_id"] = 1;
        $data["ds.vo_id"] = 1;

        // Set Vo Field data
        $data["f.field_name"] = "name";
        $data["f.field_error_msg"] = "Invalid VO name, see <a href=\"https://edms.cern.ch/file/ 503245/6/VO_Registration.pdf\" target=\"_blank\">the registration procedure</a>";
        $data["f.field_user_help"] = "<strong>Vo name</strong>

Each virtual organization must have a unique name.
The name must conform to DNS naming conventions and contain only lower-case letters.
Names within the egi.eu domain are controlled by the  EGI User Community Support Team Representatives.
Other domains may be used, but you must have the right to allocate (or obtain) names within that domain.

Minimal restrictions are:

- VO name uniqueness will be enforced through technical implementation.
- VO name should be using alphanumerical characters plus the following characters \"-\" and \".\"
- VO name <b>MUST</b> be lowercase
- VO name should not be an offending word in any language.
- VO name should not be just a sequence of characters. Especially, names like \"aaa\" will be refused.
";
        $data["f.field_admin_help"] = "Admin Healp";
        $data["f.category"] = "GENERAL INFORMATION";
        $data["f.field_link"] = "http://fieldLink.fr";
        $data["f.field_example"] = "vo.egi.eu";
        $data["f.field_regexp_id"] = 1;
        $data["f.required"] = 1;

        $data["r.report_body"] = "report body";
        $data["r.serial"] = 1;
        $data["r.created_at"] = new \DateTime("2007-06-07 00:00:00");
        $data["r.updated_at"] = new \DateTime("2015-10-27 15:17:13");

        // Set Vo Header data
        $data["vh.id"] = 1;
        $data["vh.name"] = "aegis";
        $data["vh.alias"] = "aegis";
        $data["vh.grid_id"] = 1;
        $data["vh.serial"] = 1;
        $data["vh.enrollment_url"] = "https://voms.ipb.ac.rs:8443/voms/aegis/";
        $data["vh.homepage_url"] = "https://voms.ipb.ac.rs:8443/voms/aegis/";
        $data["vh.support_procedure_url"] = "https://voms.ipb.ac.rs:8443/voms/aegis/";
        $data["vh.aup"] = "aegis-AcceptableUsePolicy-20100817-121618.pdf";
        $data["vh.aup_type"] = "file";
        $data["vh.description"] = "VO setup for grid collaboration of the Serbian scientific community.";
        $data["vh.arc_supported"] = 1;
        $data["vh.glite_supported"] = 1;
        $data["vh.unicore_supported"] = 1;
        $data["vh.globus_supported"] = 1;
        $data["vh.cloud_computing_supported"] = 1;
        $data["vh.cloud_storage_supported"] = 1;
        $data["vh.desktop_grid_supported"] = 1;
        $data["vh.validation_date"] = "2011-05-30";
        $data["vh.discipline_id"] = 1;
        $data["vh.scope_id"] = 1;
        $data["vh.status_id"] = 1;
        $data["vh.insert_date"] = "2011-05-30 10:24:30";
        $data["vh.user"] = 1;
        $data["vh.validated"] = 1;
        $data["vh.reject_reason"] = "";
        $data["vh.notify_sites"] = 1;
        $data["vh.middleware"] = array('Arc','gLite','Unicore','Globus','Cloud Computing Resources','Cloud Storage Resources','Desktop Grid');

        // Set Vo mailingList data
        $data["ml.id"] = 1;
        $data["ml.admins_mailing_list"] = "aegis-vo-admin@ipb.ac.rs";
        $data["ml.operations_mailing_list"] = "aegis-vo-operations@ipb.ac.rs";
        $data["ml.user_support_mailing_list"] = "aegis-vo-operations@ipb.ac.rs";
        $data["ml.users_mailing_list"] = "aegis-vo-users@ipb.ac.rs";
        $data["ml.security_contact_mailing_list"] = "aegis-vo-security@ipb.ac.rs";
        $data["ml.user"] = "Operations Portal";
        $data["ml.insert_date"] = "2011-05-30 10:24:30";
        $data["ml.serial"] = 1;
        $data["ml.validated"] = 1;
        $data["ml.reject_reason"] = "";
        $data["ml.notify_sites"] = 0;

        // Set Vo Metrics data
        $data["m.id"] = 1;
        $data["m.nb_vo"] = 1;
        $data["m.nb_added"] = 1;
        $data["m.nb_removed"] = 1;
        $data["m.nb_inter_vo"] = 1;
        $data["m.nb_inter_added"] = 1;
        $data["m.nb__inter_removed"] = 1;
        $data["m.day_date"] =  "2011-05-30 10:24:30";

        // Set Vo Regexp data
        $data["rxp.id"] = 1;
        $data["rxp.regexpression"] = "^([a-z0-9\-]{1,255}\.)+[a-z]{2,4}$^";
        $data["rxp.description"] ="regexp for vo name";

        // Set Vo Ressources data
        $data["r.id"] = 1;
        $data["r.serial"] = 1;
        $data["r.insert_date"] = "2011-05-30 10:24:30";
        $data["r.ram386"] = 0;
        $data["r.ram64"] = 54654;
        $data["r.job_scratch_space"] = 1456;
        $data["r.job_max_cpu"] = 1549;
        $data["r.job_max_wall"] = 1879;
        $data["r.other_requirements"] = "Other requirementsOther ";
        $data["r.cpu_core"] = 1;
        $data["r.vm_ram"] = 1;
        $data["r.storage_size"] = 1;
        $data["r.public_ip"] = 1;
        $data["r.user"] = "Pierre Frebault";
        $data["r.validated"] = 1;
        $data["r.reject_reason"] = "reason";
        $data["r.notify_sites"] = 0;
        $data["r.cvmfs"] = "a:1:{i:0;s:46:\"https://cream.ipb.ac.rs:8443/ce-cream/services\";}";


        // Set Vo Robot Certificate data
        $data["rc.id"] = 1;
        $data["rc.robot_certificate_id"] = "robot_certificate_1";
        $data["rc.serial"] = 1;
        $data["rc.service_name"] = "serviceName";
        $data["rc.service_url"] = "http://serviceUrl.fr";
        $data["rc.email"] = "email@cc.fr";
        $data["rc.robot_dn"] = "/DN=robotDn";
        $data["rc.use_sub_proxies"] = 1;

        // Set Vo Scope data
        $data["s.id"] = 1;
        $data["s.scope"] = "Global";
        $data["s.roc"] = "all";
        $data["s.decommissioned"] = 0;

        // Set Vo Status data
        $data["status.id"] = 1;
        $data["status.status"] = "Pending";
        $data["status.description"] = "";

        // Set Vo Tests data
        $data["t.test_name"] = "test";
        $data["t.roc_name"] = "aegis";

        // Set User Profile data
        $data["up.id"] = 1;
        $data["up.profile"] = "VO MANAGER";
        $data["up.description"] = "VO Manager";
        $data["up.help_msg"] = "Vo Manager Profile";

        // Set Vo Users data
        $data["u.dn"] = "/DN=UserDn";
        $data["u.email"] = "email@cc.fr";
        $data["u.vo"] = "aegis";
        $data["u.uservo"] = "";
        $data["u.ca"] = "";
        $data["u.urlvo"] = "http://url.fr";
        $data["u.last_update"] = "2015-11-05 04:51:08";
        $data["u.first_update"] = "2015-11-05 04:51:08";

        // Set Vo Users History data
        $data["uh.id"] = 1;
        $data["uh.vo"] = "aegis";
        $data["uh.u_month"] = 1;
        $data["uh.u_year"] = 2011;
        $data["uh.nbtotal"] = 452;
        $data["uh.nbremoved"] = 1;
        $data["uh.nbadded"] = 145;

        // Set Vo Users Metrics data
        $data["um.id"] = 1;
        $data["um.vo"] = "aegis";
        $data["um.discipline"] = "Multidisciplinary VOs";
        $data["um.day_date"] = "2013-01-01";
        $data["um.nbtotal"] = 452;

        // Set Vo Users Metrics CA data
        $data["umca.id"] = 1;
        $data["umca.ca"] = "/C=AM/O=ArmeSFo/CN=ArmeSFo CA";
        $data["umca.day_date"] = "2013-01-01";
        $data["umca.nbtotal"] = 452;

        // Set Voms Group data
        $data["vg.id"] = 1;
        $data["vg.group_role"] = "/aegis/Role=VO-Admin";
        $data["vg.description"] = "VO Administrators";
        $data["vg.is_group_used"] = 1;
        $data["vg.group_type"] = "Pilot";
        $data["vg.allocated_ressources"] = 1;
        $data["vg.serial"] = 1;

        // Set Voms Server data
        $data["vs.serial"] = 1;
        $data["vs.hostname"] = "voms.ipb.ac.rs";
        $data["vs.https_port"] = 8443;
        $data["vs.vomses_port"] = 15001;
        $data["vs.is_vomsadmin_server"] = 1;
        $data["vs.members_list_url"] = "https://voms.ipb.ac.rs:8443/voms/aegis/services/VOMSAdmin?method=listMembers";

        // Set Vo Yearly Validation
        $data["vy.id"] = 1;
        $data["vy.serial"] = 1;
        $data["vy.date_validation"] = "2016-01-12 13:14:17";
        $data["vy.date_last_email_sending"] = "0000-00-00 00:00:00";

        return $data;
    }
}
