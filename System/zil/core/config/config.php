<?php
namespace zil\core\config;

use zil\App;

class  Config extends App{

    /**
     * Config Options
     */
    protected  $projectBasePath = '';
    protected  $projectCacheAge = 40;
    protected  $configOptions = [ 'pageLoadStrategy'=>'default' ];

    /**
     * Database Options
     */
    protected  $dbParams = null;

    /**
     * Filesystem
     */
    protected  $curAppPath = null;
    protected  $curSysPath = null;
    protected  $viewPath = 'view/';
    protected  $templatePath = 'asset/template/';
    protected  $logPath = ['log/event','log/error','log/sys'];

    /**
     * Route Option
     */
    protected  $appRoutes = [];
    protected  $appGuardClass = null;

    
    /**
     * Log Option 
     * */
    protected  $eventLog = true;
    
    /**
     * Server Option
     */
    protected  $requestBase = null;
   
    /**
     * Cache Option
     */
    protected  $cachePath = 'data/cache/';
   
    
    public function __construct(){
       
        $this->dbParams         =   parent::$_databaseParams;
        $this->appRoutes        =   parent::$_appRoutes;
        $this->eventLog         =   parent::$_eventLog;
        
        $this->configOptions    =   parent::$_configOptions;
        $this->curSysPath       =   parent::$_curSysPath;

        if( isset(parent::$_configOptions['projectBasePath']) )
            $this->projectBasePath = parent::$_configOptions['projectBasePath'];

        $this->curAppPath       =   $_SERVER['DOCUMENT_ROOT'].$this->projectBasePath.'/'.parent::$_curAppPath;
        $this->requestBase      =   rtrim($this->projectBasePath, '/').'/'.parent::$_requestBase;

        if( isset(parent::$_configOptions['appGuardClass']) )
            $this->appGuardClass = parent::$_configOptions['appGuardClass'];

        if( isset(parent::$_configOptions['projectCacheAge']) && parent::$_configOptions['projectCacheAge'] > 0)
            $this->projectCacheAge = parent::$_configOptions['projectCacheAge'];

    }

    /**
        Generic Config Adapter
     **/
    public function getSysPath() : string {
        return $this->curSysPath;
    }

    public function getAppPath() : string {
        return $this->curAppPath;
    }

    public function getCurAppPath() : string {
        return $this->curAppPath;
    }

    public function getRequestBase() : string {
        return $this->requestBase;
    }

    public function getProjectBasePath() : string {
        return $this->projectBasePath;
    }


}

?>