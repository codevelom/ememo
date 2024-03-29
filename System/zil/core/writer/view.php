<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\ViewWriter;
use zil\core\TextProcessor;
use zil\core\scrapper\Info;

use zil\factory\Filehandler;

    class View implements ViewWriter
    {

        /**
         * Constructor
         */
        public function __construct(){}

        /**
         * Create View without conveyor
         *
         * @param Info $Info
         * @param string|null $name
         * @param string|null $controller
         * @param boolean $first
         * @return void
         */
        public function create(Info $Info, ?string $name = "index", ?string $controller = 'Home', bool $updateHostController = true){

            try{

                /**
                 * Normalize conroller name
                 */                
                $controller = ucfirst(rtrim($controller, '/'));
                $name = rtrim($name, '/');
    
                /**
                 * Details Gathering
                 */
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories\n");
                }

                if( !empty($app_base) ){
                    
                    /* View Buffering */

                    if( empty($controller) ){
                        throw new \Exception("Error: Empty controller identifier\n");
                    }   

                    /**
                     * Subsequent Creation of view requires a full update of controller
                     */
                    if(!file_exists("{$app_base}view/{$controller}/{$name}.php")) {
  
                        /**
                         * View BluePrint
                         */
                        if(!file_exists($Info->getReadPoint()."view/Template.php"))
                            die("Error: Couldn't load template view");
                     
                        if( !is_null($controller) && file_exists("{$app_base}controller/{$controller}.php") ){
                            
                            /**
                             * Set Progress Message
                             */
                            $Info->getProgressMessage("createView");

                            (new Filehandler())->createFile("{$app_base}view/{$controller}/{$name}.php", "<?php \n\n\n  ?>\n<p>{$name} works</p>");

                            if($updateHostController){
                                print "updating {$controller} controller...\n";
                                
                                $filehandler = new Filehandler;

                                $context = null;
                                $hostcontroller = $controller;

                                $buffering_pont = "{$app_base}controller/{$hostcontroller}.php";
                                $context_handle = fopen("{$buffering_pont}", 'rb');
                            
                                /**
                                 * Prepare new View Conveyor
                                 */
                                $view = "\t\tpublic function {$name}(Param \$param){\n\n\t\t\t\$OutputData = [];\n\n\t\t\t#render the desired interface inside the view folder\n\n\t\t\tView::render(\"{$controller}/{$name}.php\", \$OutputData);\n\t\t}\n\n";
                                
                                $first = true; 
                                $halt_writing = false;

                                while ($blueprint = fgets($context_handle) ) {
                                    
                                    /**
                                     * Find first function in the controller class and insert conveyor before first
                                     */
                                    $pattern = "/^(public|private|protected)?[\s]*function[\s]+(.)+(\{)?$/i";
                                        
                                    if ( preg_match($pattern, trim($blueprint), $match) != 0 && $halt_writing == false) {
                                            
                                        if($first){
                                            
                                            $context .= $view.$blueprint;
                                            $halt_writing = true;
                                        }
                                    }else{
                                        $context .= $blueprint; 
                                    }
                                }
                                
                                /**
                                 * Recreate Controller File
                                 */
                                $filehandler->createFile("{$app_base}controller/{$hostcontroller}.php", $context);
                                
                                unset( $context, $blueprint);
                                fclose($context_handle);
                            }
                        }
                    }
                }else{
                    echo "Error: Couldn't create {$name} view, {$name} exists\n";
                }
            }catch(\Exception $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                unset( $context, $blueprint);
                

                print "View creation closed\n";
            }
        }

        /**
         * Views created are independent
         *
         * @param Info $Info
         * @param string|null $name
         * @return void
         */
        public function createWithoutConveyor(Info $Info, ?string $name = 'index'){

            try{

                /**
                 * Details Gathering
                 */
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories\n");
                }

                if( !empty($app_base) ){
                    
                    /* View Buffering */

                    

                   
                      if( !file_exists("{$app_base}view/{$name}.php") ){
                            
                            /**
                             * Set Progress Message
                             */
                            $Info->getProgressMessage("createView");

                            (new Filehandler())->createFile("{$app_base}view/{$name}.php", "<?php \n\n\n  ?>\n<p>{$name} works</p>");

                          
                            
                        }else{
                                throw new \Exception("Error: file already exists\n");
                        }
                }
               
            }catch(\Exception $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                print "View creation closed\n";
            }
        }

        /**
         * Destroy A View and Remove its conveyor
         *
         * @param Info $Info
         * @param string $viewName
         * @param string|null $hostcontroller
         * @return void
         */
        public function destroy (Info $Info, string $viewName, ?string $hostcontroller){
            try{

                /**
                 * Details Gathering
                 */
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();

                    $hostcontroller = ucfirst($hostcontroller);
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }


                if( !is_null($hostcontroller) && file_exists("{$app_base}controller/{$hostcontroller}.php") ){
                    
                    /**
                     * Remove View conveyor and delete the view
                     */
                    print "updating {$hostcontroller} controller...\n";

                    $context = null;

                    $blueprinting_source_handle = fopen("{$app_base}controller/{$hostcontroller}.php", 'r+');

                    $function_open = false;
                    $expression_open = false;
                    $host_controller_updated = false;

                    while($blueprint = fgets($blueprinting_source_handle)){

                        if($function_open){
                           
                            $host_controller_updated = true;

                            if(preg_match('/[\w][\s]*\(.+\)[\n]*\{/i', $blueprint, $m)){
            
                                $expression_open = true; 
                                $write = null;
                            }else{
                                if($expression_open){
                                    if(preg_match('/[\s\S]*\}/i', $blueprint, $m)){
                                        
                                        $expression_open = false;
                                    }
                                }else{
                                    if(preg_match('/[\s\S]*\}/i', $blueprint, $m)){
                                        
                                        $function_open = false;
                                    }
                                }
                                $write = null;
                            }
                            
                        }else{
                            if(preg_match("/(public|protected|private)[\s]+function[\s]+{$viewName}[\s]*\(.*\)/i", $blueprint, $matches)){
                                $function_open = true;
                                
                                $write = null;
                            }else{
                                $write = $blueprint;
                            }    
                        }
                        
                        $context .= $write;
                    }
                    
                    fclose($blueprinting_source_handle);
                    if ( $host_controller_updated ) {
                    
                        file_put_contents("{$app_base}controller/{$hostcontroller}.php", $context);
                        print "{$hostcontroller} updated\n"; 
                        
                        unset( $function_open, $expression_open, $host_controller_updated );
                    }else{
                        print "{$hostcontroller} not updated\n"; 
                    }
                }else{
                    throw new \Exception("Error: Couldn't found host controller for {$viewName} view");
                }

                
            }catch(\Exception $e){
                
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                @unlink("{$Info->getAppDir()}src/{$app_name}/view/{$hostcontroller}/{$viewName}.php");
                print "destruction closed\n";
            }
        }
    }
?>