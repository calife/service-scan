<?php

/**
 * Definisce i Dao necessari alla memorizzazione dei dati del servizio Service Scan.
 * @author pucci
 * @date mercoledì, 07. maggio 2014
 **/

interface DaoI {

  public function load($pk); /* Get Domain object by primary key */
  public function queryAll(); /* Get all records from table */	
  public function queryAllOrderBy($orderColumn,$desc); /* Get all records from table ordered by field */	
  public function delete($pk); /* Delete record from table */
  public function insert($entity); /* Insert record to table */	
  public function update($entity);	/* Update record in table */
  public function clean(); /* Delete all rows */

  public function queryByField($fieldName,$fieldValue);
  public function deleteByField($fieldName,$fieldValue);

}