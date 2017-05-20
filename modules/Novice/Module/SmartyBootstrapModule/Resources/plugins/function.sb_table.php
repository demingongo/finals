<?php
/**
 * Smarty plugin
 */

/**
 * Smarty {sb_table} function plugin
 * Type:     function<br>
 * Name:     sb_table<br>
 * Purpose:  build bootstrap table
 *
 * Params:
 * <pre>
 * - message	- string 
 * - class      - string
 * </pre>
 *
 * @author   Demingongo-Litemo Stephane
 *
 * @param array                    $params   parameters (array $columns, array $entities)
 *
 * @return string  Display Bootstrap table the table
 */

function smarty_function_sb_table($params, &$smarty)
{	

    $accessor = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor();

    $isManagement = isset($params['management']) && $params['management'] === true;

    $table = '';

    $table = '<table id="tab" class="table table-striped table-hover ">';

    $table .= '<thead><tr>';


    if($isManagement){
        $table .= '<th>#</th>
        <th>
            <input type="checkbox" id="checkAll" name="checkall-toggle" data-novice-toggle="checkall" title="check all" />
        </th>';
    }

    foreach($params['columns'] as $column){
        $table .= ('<th class="'.(isset($column['class']) ? $column['class'] : '').'">'.(isset($column['label']) ? $column['label'] : $column['property']).'</th>');
    }

    $table .= '</tr></thead>';
    
    $table .= '<tbody>';

    $i = 0;
    foreach($params['items'] as $entity){
        $table .= '<tr>';

        if($isManagement){
            $table .= '<td>'.$entity['id'].'</td>
            <td>
                <input type="checkbox" id="cb'.$i.'" name="cid[]" value="'.$entity['id'].'" />
            </td>';
        }

        foreach($params['columns'] as $column){
            $prop = $column['property'];

            $propertyValue = $accessor->getValue($entity, $prop);

            $table .= ('<td class="'.(isset($column['class']) ? $column['class'] : '').'">');

            if(isset($column['filter'])){
                $table .= $column['filter']($propertyValue, $entity, $i, $smarty);
            }
            else if(isset($column['route']) && is_array($column['route'])){
                $route = $column['route'];

                if(isset($route['params']) && is_array($route['params'])){
                    foreach($route['params'] as $param => $value){
                        if($value instanceof \Novice\Module\SmartyBootstrapModule\Util\ItemProperty && $accessor->isReadable($entity, $value->getPropertyName())){
                            $route['params'][$param] = $accessor->getValue($entity, $value->getPropertyName());
                        }
                    }
                }
                
                $table .= '<a href="'.$smarty->getContainer()->get('templating.route_function')->execute($route, $smarty).'">';
                $table .= $propertyValue;
                $table .= '</a>';
            }
            else{
                $table .= $propertyValue;
            }

            $table .= ('</td>');

        }
        
        $table .= ('</tr>');
        $i++;
    }

    $table .= '</tbody>';

    $table .= '</table>';

    return $table;
}

?>