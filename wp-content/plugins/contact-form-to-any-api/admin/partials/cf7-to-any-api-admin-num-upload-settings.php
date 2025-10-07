<?php 
function api_additional_settig( $cf7anyapi_form_field, $cf7anyapi_numaric_field = '', $cf7anyapi_upload_field = ''){
    empty($cf7anyapi_numaric_field) ? $cf7anyapi_numaric_field = [] : '';
    empty($cf7anyapi_upload_field) ? $cf7anyapi_upload_field = [] : '';
    $add_html = '';
    $add_html .= '<div class="cf7anyapi_confing_add_setting cf7anyapi_full_width" id="cf7anyapi_confing_add_setting" >';
    if(!empty($cf7anyapi_form_field) ){
        $add_html .= '<h3>'. __( 'Additional Setting', 'contact-form-to-any-api' ) .'</h3>';
        $add_html .= '<div class="cf7anyapi_field">';
        $add_html .= '<label for="cf7anyapi_numaric_field">' . __( 'Choose the fields you would like to send as integers/numerics to your API (optional).', 'contact-form-to-any-api' ) . '</label>';
        $add_html .= '<select name="cf7anyapi_numaric_field[]" id="cf7anyapi_numaric_field" multiple>';
        $add_html .= '<option value="" disabled>' . __( 'Choose the fields', 'contact-form-to-any-api' ) . '</option>';
        foreach($cf7anyapi_form_field as $cf7anyapi_form_field_key => $cf7anyapi_form_field_value){
            $add_html .= '<option value="' . esc_attr($cf7anyapi_form_field_key) . '" ' . (in_array($cf7anyapi_form_field_key, $cf7anyapi_numaric_field ) ? 'selected' : '') . '>' . esc_attr($cf7anyapi_form_field_key) . '</option>';
        }
        $add_html .= '</select>';
        $add_html .= '</div>';
        $add_html .= '<div class="cf7anyapi_field">';
        $add_html .= '<label for="cf7anyapi_upload_field">' . __( 'Choose multiple file upload field (optional).', 'contact-form-to-any-api' ) . '</label>';
        $add_html .= '<select name="cf7anyapi_upload_field[]" id="cf7anyapi_upload_field" multiple>';
        $add_html .= '<option value="" disabled>' . __( 'Choose the fields', 'contact-form-to-any-api' ) . '</option>';
        foreach($cf7anyapi_form_field as $cf7anyapi_form_field_key => $cf7anyapi_form_field_value){
            $add_html .= '<option value="' . esc_attr($cf7anyapi_form_field_key) . '" ' . (in_array($cf7anyapi_form_field_key, $cf7anyapi_upload_field ) ? 'selected' : '') . '>' . esc_attr($cf7anyapi_form_field_key) . '</option>';
        }
        $add_html .= '</select>';
        $add_html .= '</div>';
    }
    $add_html .= '</div>';
    return $add_html;
}