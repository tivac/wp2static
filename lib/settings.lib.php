<?php
namespace wp2static\settings;

use wp2static;

function create($args = array()) {
    $defaults = array(
        'type'    => 'text',
        'section' => 'main',
        'choices' => array(),
        'class'   => '',
        'render'  => 'wp2static\settings\render'
    );
    
    $args = wp_parse_args($args, $defaults);
    
    $options = array(
        'id'        => $args['id'],
        'type'      => $args['type'],
        'desc'      => $args['desc'],
        'choices'   => $args['choices'],
        'label_for' => $args['id'],
        'class'     => $args['class']
    );
    
    if($type == 'text' && !strlen($options['class'])) {
        $options['class'] = 'regular-text';
    }
    
    add_settings_field(
        $args['id'],        //unique id for this setting
        $args['title'],     //setting title text (or label)
        $args['render'],    //render callback
        wp2static\SETTINGS, //page this setting will be added to
        $args['section'],   //section for this setting
        $options            //any custom args for the render callback
    );
}

function render($args = array()) {
    extract($args);
    
    $option = wp2static\NAME;
    $options = get_option($option);
    
    if (!isset($options[$id]) && 'type' != 'checkbox') {
        $options[$id] = $std;
    }
    
    $class = ($class != '') ? ' class="' . $class . '"' : '';
    
    switch ($type) {
        case 'heading':
            echo "</td></tr><tr valign='top'><td colspan='2'>{$desc}";
            break;
        
        case 'checkbox':
            $checked = (isset($options[$id]) && $options[$id] == 'on') ? ' checked="checked"' : '';
            echo "<label><input{$class} type='checkbox' id='{$id}' name='{$option}[{$id}]' value='on'{$checked} /> {$desc}</label>";
            
            $desc = '';
            break;
        
        case "multi-checkbox":  
            foreach($choices as $value => $label) {  
                $checked = ($options[$id][$value] == 'true') ? 'checked="checked"' : '';
                
                echo "<label><input${class} type='checkbox' id='{$id}_{$value}' name='{$option}[{$id}][{$value}]' value='1' {$checked} /> {$label}</label><br/>";
            }  
            
            echo ($desc != '') ? '<br />' : '';
        break;
        
        case 'select':
            echo "<select{$class} name='{$option}[{$id}]'>";
            
            foreach ($choices as $value => $label) {
                $selected = ($options[$id] == $value) ? 'selected="selected"' : '';
                
                echo "<option value='{$value}' ${selected}>{$label}</option>";
            }
            
            echo "</select>";
            break;
        
        case 'radio':
            $i = 0;
            foreach ($choices as $value => $label) {
                $selected = ($options[$id] == $value) ? ' selected=\'selected\'' : '';
                echo "<input{$class} type='radio' name='{$option}[{$id}]' id='{$id}{$i}' value='{$value}'> <label for='{$id}{$i}'>{$label}</label>";
                
                if ($i < count($choices) - 1) {
                    echo "<br />";
                }
                $i++;
            }
            break;
        
        case 'textarea':
            echo "<textarea{$class} id='{$id}' name='{$option}[{$id}]' placeholder='{$std}'>{$options[$id]}</textarea>";
            break;
        
        case 'password':
            echo "<input{$class} type='password' id='{$id}' name='{$option}[{$id}]' value='{$options[$id]}' />";
            break;
        
        case 'text': //fall-through
        default:
            echo "<input{$class} type='text' id='{$id}' name='{$option}[{$id}]' placeholder='{$std}' value='{$options[$id]}' />";
            break;
        
    }
    
    if($desc != '') {
        echo "<span class='description'>{$desc}</span>";
    }
    
    echo "\n";
}
