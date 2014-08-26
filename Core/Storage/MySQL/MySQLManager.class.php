<?php
/**
 *  SkyData: CMS Framework   -  22/Aug/2014
 *
 * Copyright (C) 2014  Ernesto Giralt (egiralt@gmail.com)
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @Author: E. Giralt
 * @Date:   22/Aug/2014
 * @Last Modified by:   E. Giralt
 * @Last Modified time: 22/Aug/2014
 */
namespace SkyData\Core\Storage\MySQL;

use \SkyData\Core\SkyDataObject;

use \SkyData\Core\Storage\IStorage;

class MySQLManager extends SkyDataObject implements IStorage 
{

    const MYSQL_DEFAUL_PORT = 3306;
        
    protected $Database;
    protected $ConnectOptions;
    protected $ConnectionName;
    
    public function __construct($connectionName = null, $options = array())
    {
        if ($connectionName != null)
            $this->Connect($connectionName, $options);    
    }
    
    public function GetStorageType ()
    {
        return 'mysql';
    }
    
    public function GetStorageConfiguration ()
    {
        $type = $this->GetStorageType();
        $storageConfig = $this->GetApplication()->GetConfigurationManager()->GetMapping('storage');
        if (!empty($storageConfig[$type]))
        {
            return $storageConfig[$type];
        }
        else
            return null;
    }
    
    /**
     * Conecta a la base de datos
     * 
     * @param array $parametersSectionName Nombre de la sección de la configuración que contiene los datos de conexión
     */
    public function Connect ($connectionName = 'default', $options = array())
    {
        
        $storageConfig = $this->GetStorageConfiguration();
        if ($storageConfig != null &&  !empty($storageConfig[$connectionName]))
        {
            $configOptions = $storageConfig[$connectionName];
            
            if (!isset($this->Database)) 
            {
                
                $this->Database = new \mysqli (
                        $configOptions['server'],
                        $configOptions['user'],
                        $configOptions['password'],
                        $configOptions['database'],
                        !empty($configOptions['port']) ? $configOptions['port'] : MySQLManager::MYSQL_DEFAUL_PORT
                 );
                 
                if($this->Database->connect_errno)
                {
                    $message = sprintf('Error #%s:%s', $this->Database->connect_errno, $this->Database->connect_error);
                    throw new \Exception($message, 1);//TODO: Agregarle el mensaje que devuelve el server
                }
                
                $this->ConnectionName = $connectionName;
                foreach ($options as $optionName => $optionValue) {
                    $status = false;
                    switch ($optionName) {
                        case 'autocommit':  $status = $this->Database->autocommit ($optionValue); break;
                        case "charset":     $status = $this->Database->set_charset($optionValue); break;
                    }
                    if (!$status)
                        throw new \Exception("Error inicializando opciones: [$optionName]", 1);
                }                   
                
            }
            $this->ConnectOptions = $configOptions;
            
            return true;
        }
        else
            throw new \Exception("Error en los parámetros de configuración de la base de datos", 1);
             
    }
                        
    /**
     * 
     */
    public function GetOne ($object, $value, $id = 'id', $fields = null)
    {
        $query = 'SELECT '.($fields !== null && is_array($fields) ? join(', ', $fields) : '*' );
        $query .= "FROM $object WHERE $id=$value LIMIT 1";
        if ($rset = $this->Database->query($query))
            $result = $rset->fetch_object();
        
        return $result;   
    }
    
    public function GetAll ($object, $fields = null)
    {
        $query = 'SELECT '.($fields !== null && is_array($fields) ? join(', ', $fields) : '*' );
        $query .= " FROM $object;";
        
        $result = array();
        if ($rset = $this->Database->query($query))
            while ($row = $rset->fetch_object())
                $result[] = $row;
        
        return $result;   
    }
    
    public function Match ($query, $count)
    {
        $result = array();
        
        if (!preg_match('/\s?LIMIT\s/', $query))
            $query.= " LIMIT $count";
        if ($rset = $this->Database->query($query)) {
        
            while ($obj = $rset->fetch_object())
                $result[] = $obj;
            
        }
        
        return $result;   
    }
    
    public function GetLastError ()
    {
        return mysql_error();
    }
  
    public function Disconnect ()
    {
        if(isset($this->Database))
        {
            $this->Database->close();
            $this->Database = null;
        }
    }     
    
    public function Version ()
    {
        return $this->Query("SELECT version() version");
    }
    
    public function ObjectExists ($object)
    {
       $result = false;
       
        if (isset($this->Database)) 
        {
            $info = $this->Query("SHOW TABLES LIKE ".$object);
            $result = !empty($info); 
        }

        return $result;   
    }
    
} 