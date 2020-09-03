<?php namespace CheckEmail\Util;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

function wpchill_sanitize_check_email( $email, $multiple = true ) {
	$emails = explode( ',', $email );
	if ( ! $multiple ) {
		$emails = array_slice( $emails, 0, 1 );
	}

	$cleaned_emails = array_map( __NAMESPACE__ . '\\wpchill_sanitize_check_email_with_name', $emails );

	return implode( ', ', $cleaned_emails );
}

function wpchill_sanitize_check_email_with_name( $string ) {
	$string = trim( $string );

	$bracket_pos = strpos( $string, '<' );
	if ( false !== $bracket_pos ) {
		if ( $bracket_pos > 0 ) {
			$name = substr( $string, 0, $bracket_pos );
			$name = trim( $name );

			$email = substr( $string, $bracket_pos + 1 );
			$email = str_replace( '>', '', $email );

			return sanitize_text_field( $name ) . ' <' . \wpchill_sanitize_check_email( $email ) . '>';
		}
	}

	return \wpchill_sanitize_check_email( $string );
}

function wp_chill_check_email_is_admin_non_ajax() {
	if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
		return false;
	}

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	return is_admin();
}

function wp_chill_check_email_array_checked( $values, $current ) {
	if ( ! is_array( $values ) ) {
		return;
	}

	if ( in_array( $current, $values, true ) ) {
		echo "checked='checked'";
	}
}

function wp_chill_check_email_get_dismiss_icon() {
	return <<<EOT
<span class="dashicons dashicons-dismiss"></span>
EOT;
}

function wp_chill_check_email_get_confirm_icon() {
	return <<<EOT
<span class="dashicons dashicons-yes-alt"></span>
EOT;

}

function wp_chill_check_email_stringify( $may_be_array, $delimiter = ',' ) {
	if ( ! is_array( $may_be_array ) ) {
		return (string) $may_be_array;
	}

	return implode( $delimiter, $may_be_array );
}

function wp_chill_check_email_get_user_defined_date_format() {
	return sprintf( '%1$s %2$s', get_option( 'date_format', 'Y-m-d' ), get_option( 'time_format', 'g:i a' ) );
}

function wp_chill_check_email_el_array_get( $array, $key, $default = null ) {
	return isset( $array[ $key ] ) ? $array[ $key ] : $default;
}

function wp_chill_check_email_advanced_search_term( $term ) {
	if ( ! is_string( $term ) ) {
		return false;
	}

	$predicates = wp_chill_check_email_get_advanced_search_term_predicates( $term );

	return ! empty( $predicates );
}

function wp_chill_check_email_get_advanced_search_term_predicates( $term ) {
	if ( ! is_string( $term ) ) {
		return array();
	}

	$predicates           = explode( ' ', $term );
	$predicates_organized = array();

	foreach ( $predicates as $predicate ) {
		$is_match = preg_match( '/(id|email|to|cc|bcc|reply-to):(.*)$/', $predicate, $matches );
		if ( 1 === $is_match ) {
			$predicates_organized[ $matches[1] ] = $matches[2];
		}
	}

	return $predicates_organized;
}

function wp_chill_check_email_get_advanced_search_url() {
	$admin_url = get_admin_url( null, 'admin.php?page=email-log' );

	return add_query_arg( 'el_as', 1, $admin_url );
}

function wp_chill_check_email_get_column_label_by_db_column( $db_column ) {
	return wp_chill_check_email_get_column_label( $db_column );
}

function wp_chill_check_email_get_column_label( $column_name ) {
	$labels = wp_chill_check_email_get_column_label_map();

	if ( ! array_key_exists( $column_name, $labels ) ) {
		return $column_name;
	}

	return $labels[ $column_name ];
}

function wp_chill_check_email_get_column_label_map() {
	$labels = array(
		'id'          => __( 'ID', 'email-log' ),
		'to_email'    => __( 'To', 'email-log' ),
		'subject'     => __( 'Subject', 'email-log' ),
		'message'     => __( 'Message', 'email-log' ),
		'attachments' => __( 'Attachment', 'email-log' ),
		'sent_date'   => __( 'Sent at', 'email-log' ),
		'from'        => __( 'From', 'email-log' ),
		'cc'          => __( 'CC', 'email-log' ),
		'bcc'         => __( 'BCC', 'email-log' ),
		'reply_to'    => __( 'Reply To', 'email-log' ),
		'ip_address'  => __( 'IP Address', 'email-log' ),
		'result'      => __( 'Sent Status', 'email-log' ),
	);

	return apply_filters( 'el_db_column_labels', $labels );
}
