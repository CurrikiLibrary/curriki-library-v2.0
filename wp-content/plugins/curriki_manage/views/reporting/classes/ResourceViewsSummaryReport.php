<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceViewsSummaryReport
 *
 * @author waqarmuneer
 */
class ResourceViewsSummaryReport {
        
    public static function get_pageviews($contributorid, $start_date, $end_date , $collection_slug = "") {
        global $wpdb;
        
        $query_vars = array();        
        
        $where_clause_for_main_query = "where r.contributorid = %d";
        $query_vars[0] = intval($contributorid);
        
        $sql_date_range = "";
        if( !($start_date == null && $end_date == null) ){
            $sql_date_range = "and DATE(rv.viewdate) >= DATE(%s) and DATE(rv.viewdate) <= DATE(%s)";
            $query_vars[1] = $start_date;
            $query_vars[2] = $end_date;
        }
        
        $sql_union_collection_elements = "";        
        /******* If summary filter by 'collection slug' *********/
        if( strlen(trim($collection_slug)) > 0 )
        {
            $where_clause_for_main_query = "where r.pageurl = %s";
            $query_vars[0] = trim($collection_slug);
            
            $sql_union_collection_elements = "  UNION
                                                select ifnull(COUNT(rv.resourceid), 0) as pageviews, r.resourceid, r.type , 'secondary' as record_type
                                                FROM collectionelements ce
                                                left outer join resources r on ce.resourceid = r.resourceid
                                                left outer join resources cr on ce.collectionid = cr.resourceid
                                                left outer join resourceviews rv on ce.resourceid = rv.resourceid
                                                where cr.pageurl = %s
                                                {$sql_date_range}
                                                group by r.resourceid
                                            ";
            if( array_key_exists(1, $query_vars) && array_key_exists(2, $query_vars)){
                $query_vars[3] = trim($collection_slug);
                $query_vars[4] = $start_date;
                $query_vars[5] = $end_date;
            }else{
                $query_vars[1] = trim($collection_slug);
            }
        }
        
        /******** Main query to get unique users **********/
        $query = "
                    SELECT ifnull(COUNT(rv.resourceid), 0) as pageviews, r.resourceid, r.type , 'primary' as record_type
                    FROM resources r
                    left outer join resourceviews rv on r.resourceid = rv.resourceid
                    {$where_clause_for_main_query}
                    {$sql_date_range}
                    group by r.resourceid                    
                    {$sql_union_collection_elements}
            ";
        $result = $wpdb->get_results($wpdb->prepare($query, $query_vars));        
        return self::calculate_pageviews($result);
    }
    
    public static function calculate_pageviews_for_collection_filter($result) {
        $collecitons_arr = array();
        $resources_arr = array();            
        foreach ($result as $row) {
                        
            if($row->record_type === 'primary'){
                $collecitons_arr[] = $row;
            }elseif($row->record_type === 'secondary'){
                $resources_arr[] = $row;
            }                        
        }        
        
        $data_collections = self::calculate_pageviews($collecitons_arr);
        $data_resources = self::calculate_pageviews($resources_arr);            
       
        $collections_pageviews_sum = $data_collections['resources_pageviews_sum'] + $data_collections['collections_pageviews_sum'];
        $resources_pageviews_sum = $data_resources['resources_pageviews_sum'] + $data_resources['collections_pageviews_sum'];
        $resources_pageviews_sum_text = "$resources_pageviews_sum <br /> Resources ({$data_resources['resources_pageviews_sum']}) <br /> Collections ({$data_resources['collections_pageviews_sum']})";
        return array('collections_pageviews_sum' => $collections_pageviews_sum , 'resources_pageviews_sum' => $resources_pageviews_sum_text);   
    }
    
    public static function calculate_pageviews($result) {        
        $pageviews_resources_arr = array();
        $pageviews_collections_arr = array();
        foreach ($result as $record) {
            if ($record->type === 'resource') {
                $pageviews_resources_arr[] = $record->pageviews;
            } elseif ($record->type === 'collection') {
                $pageviews_collections_arr[] = $record->pageviews;
            }
        }
        
        $resources_pageviews_sum = array_sum($pageviews_resources_arr);
        $collections_pageviews_sum = array_sum($pageviews_collections_arr);
        return array("resources_pageviews_sum" => $resources_pageviews_sum, 'collections_pageviews_sum' => $collections_pageviews_sum);
    }    
    
}
