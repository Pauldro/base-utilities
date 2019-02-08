<?php
    namespace Pauldro\Util;

    /**
     * Methods for getting, setting, isset() properties
     */
    trait MagicMethods {
        /**
         * Function for setting property values
         * 1. Checks if Property exists or is an alias
         * 2. Sets the value using method set_{$property} or setting it directly if no set_ function exists
         * @param string $property Property Name
         * @param bool             
         */
        public function set($property, $value) {
            $classproperty = false;

            if (property_exists($this, $property)) {
                $classproperty = $this->property;
            } elseif (isset($this->aliases)) {
                if (array_key_exists($property, $this->aliases)) {
                    $classproperty = $this->aliases[$property];
                }
            }

            if (!empty($classproperty)) {
                $setmethod = "set_$classproperty";
                if (method_exists($this, $setmethod)) {
                    $this->$setmethod($value);
                } else {
                    $this->$classproperty = $value;
                }
                return true;
            } else {
                $this->error("This property or alias ($property) does not exist");
                return false;
            }
        }

        /**
         * Properties are protected from modification without the set() function, but are still allowed
         * to get property values
         * 1. Check if we have a get_{$property} method
         * 2. Check if Property exists or if we can look up an alias
         * 3. Return Property or Error
         * @param  string $property The property trying to be accessed
         * @return mixed		    Property value or Error
         */
        public function __get($property) {
            $method = "get_{$property}";
            $classproperty = false;

            if (method_exists($this, $method)) {
                return $this->$method();
            } else {
                if (property_exists($this, $property)) {
                    $classproperty = $property;
                } elseif (isset($this->aliases)) {
                    $classproperty = $this->aliases[$property];
                }

                if (!empty($classproperty)) {
                    return $this->$classproperty;
                } else {
                    $this->error("This property or alias ($property) does not exist");
                    return false;
                }
            }
        }

        /**
         * Is used to PHP functions like isset() and empty() get access and see
         * if variable is set
         * @param  string  $property Property Name
         * @return bool		   Whether Property is set
         */
        public function __isset($property){
            return isset($this->$property);
        }

        /**
         * Returns if this object has the property $property
         * @param string $property Property to validate if object has it
         * @return bool            Does this object contain $property as property
         */
        public function has_property($property) {
            return property_exists(__CLASS__, $property);
        }
    }

    