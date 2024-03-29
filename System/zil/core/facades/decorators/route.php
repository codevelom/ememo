<?php
namespace zil\core\facades\decorators;

use zil\core\config\Config;
use zil\core\tracer\ErrorTracer;



    trait Route_D1{
   
        /**
         * Add prefix to route
         *
         * @param string $prefix
         * @param array $routes
         * @return array
         */
        public function prefix(string $prefix, array $routes = []): array{ 
            /**
             * Extract Routes
             */

             try{
                foreach($routes as $route => $resource){
                    
                    /**
                     * Generate new keys
                     */
                    $key = '';
                    foreach( explode('|', $route) as $alias ){
                        $key .= $prefix.trim($alias).'|';
                    }               

                    /**
                     * Normalize key
                     */
                    $key = rtrim($key, '|');

                    /**
                     * Remove Existing key
                     */
                    unset($routes[$route]);
                    
                    /** 
                     * Assign new key
                    */
                    $routes[$key] = $resource;
                }

                return $routes;
            }catch(\OutOfBoundsException $t){
                new ErrorTracer($t);
            }catch(\Throwable $t){
                new ErrorTracer($t); 
            }
        }

        /**
         * Converge all pieces of routes
         *
         * @param array ...$routes
         * @return array
         */
        public function merge(array ...$routes): array{
            try{
                return array_merge(...$routes);
            }catch(\TypeError $t){
                new ErrorTracer($t);
            }catch(\LengthException $t){
                new ErrorTracer($t);
            }catch(\LogicException $t){
                new ErrorTracer($t);
            }catch(\throwable $t){
                new ErrorTracer($t);
            }
            
        }

        /**
         * Detect Ip of user connecting to the app
         *
         * @return string
         */
        public function ipDetect(): string{
            try{

                $ip = '';

                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }
                
                elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
                    $ip = $_SERVER['HTTP_X_REAL_IP'];
                }
                
                else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }
                    
                return $ip;

            }catch(\Throwable $t){
                new ErrorTracer($t);
            }
                
        }
        
    }

    

?>