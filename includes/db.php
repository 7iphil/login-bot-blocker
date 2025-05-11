<?php

// namespace LBBKR;

// class DB {
    
//     private $table;

//     public function __construct() {

//         global $wpdb;

//         $this->table = esc_sql($wpdb->prefix . 'login_bot_blocker');
        
//     }

//     public function maybe_create_table() {

//         global $wpdb;
        
//         $charset_collate = $wpdb->get_charset_collate();

//         $sql = "CREATE TABLE IF NOT EXISTS $this->table (
//             id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
//             ip VARCHAR(45) NOT NULL,
//             blocked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
//             PRIMARY KEY (id),
//             UNIQUE(ip)
//         ) $charset_collate;";

//         require_once ABSPATH . 'wp-admin/includes/upgrade.php';

//         dbDelta($sql);

//     }

//     public function block_ip($ip) {

//         global $wpdb;
        
//         // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- This is a safe database write operation using $wpdb->replace(), which is acceptable for internal plugin logic.
//         $wpdb->replace(
//             $this->table,
//             ['ip' => $ip]
//         );
    
//         wp_cache_delete('lbbkr_blocked_ips', 'lbbkr');

//     }

//     public function get_blocked_ips() {

//         global $wpdb;
    
//         $cache_key = 'lbbkr_blocked_ips';
    
//         $data = wp_cache_get($cache_key, 'lbbkr');
    
//         if ($data === false) {
    
//             $safe_table = esc_sql($this->table);
    
//             $sql = "SELECT * FROM " . $safe_table . " ORDER BY blocked_at DESC";
            
//             // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery -- The table name is already sanitized via esc_sql() and does not contain user input; direct query is safe here.
//             $data = $wpdb->get_results($sql);
    
//             wp_cache_set($cache_key, $data, 'lbbkr', 600); // 10 min

//         }
    
//         return $data;
//     }

//     public function delete_ip($ip) {

//         global $wpdb;
        
//         // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- This is a simple delete operation with no user-provided input in the SQL structure; it's safe to use without prepare().
//         $wpdb->delete(
//             $this->table,
//             ['ip' => $ip]
//         );
    
//         wp_cache_delete('lbbkr_blocked_ips', 'lbbkr');

//     }

//     public function clear_all() {

//         global $wpdb;
//         // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- The table name is already sanitized via esc_sql() and passed safely through prepare(). No user input involved.
//         $wpdb->query(
//             $wpdb->prepare(
//                 "TRUNCATE TABLE %s",
//                 $this->table
//             )
//         );
    
//         wp_cache_delete('lbbkr_blocked_ips', 'lbbkr');

//     }

//     public function is_blocked($ip) {

//         global $wpdb;
    
//         $cache_key = "lbbkr_is_blocked_{$ip}";
//         _fttg('ip ' . $ip);
//         $blocked = wp_cache_get($cache_key, 'lbbkr');
    
//         if (false === $blocked) {
            
//             // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- This is a read-only query that checks if an IP is blocked; it's used internally and is considered safe.
//             $blocked = (bool) $wpdb->get_var(
//                 $wpdb->prepare(
//                     "SELECT COUNT(*) FROM %s WHERE ip = %s",
//                     $this->table,
//                     $ip
//                 )
//             );
//             _fttg('$blocked ' . $blocked);
//             wp_cache_set($cache_key, $blocked, 'lbbkr', 300); // 5 min
//         }
    
//         return $blocked;

//     }

//     public function drop_table() {

//         global $wpdb;
    
//         /**
//          * Disabling WPCS rules for DROP TABLE statement as this is a controlled schema change during plugin uninstallation or maintenance.
//          * The table name is already sanitized and prepared using wpdb->prepare(), so it's considered safe.
//          */
//         // phpcs:disable WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
//         $wpdb->query(
//             $wpdb->prepare(
//                 "DROP TABLE IF EXISTS %s",
//                 $this->table
//             )
//         );
//         // phpcs:enable WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
//     }
// }


namespace LBBKR;

class DB {
    
	private $table;

	public function __construct() {
		global $wpdb;
		$this->table = esc_sql( $wpdb->prefix . 'login_bot_blocker' );
	}

	public function maybe_create_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $this->table (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			ip VARCHAR(45) NOT NULL,
			blocked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE(ip)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	public function block_ip( $ip ) {

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- REPLACE is safe here for inserting or updating known IP
		$wpdb->replace( $this->table, [ 'ip' => $ip ] );

        wp_cache_delete( 'lbbkr_blocked_ips', 'lbbkr' );

	}

public function get_blocked_ips() {

	global $wpdb;

	$cache_key = 'lbbkr_blocked_ips';

	$data = wp_cache_get( $cache_key, 'lbbkr' );

	if ( false === $data ) {

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- safe internal query
		$data = $wpdb->get_results( "SELECT * FROM {$this->table} ORDER BY blocked_at DESC" );
		wp_cache_set( $cache_key, $data, 'lbbkr', 300 ); // 5 min

	}

	return $data;

}

	public function delete_ip( $ip ) {

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Safe delete from known internal table with sanitized input
		$wpdb->delete( $this->table, [ 'ip' => $ip ] );

        wp_cache_delete( 'lbbkr_blocked_ips', 'lbbkr' );

	}

	public function clear_all() {

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table is internal, TRUNCATE is intentional
		$wpdb->query( "TRUNCATE TABLE {$this->table}" );

        wp_cache_delete( 'lbbkr_blocked_ips', 'lbbkr' );
        
	}

    public function is_blocked( $ip ) {

        global $wpdb;
    
        $cache_key = 'lbbkr_block_' . md5( $ip );

        $result = wp_cache_get( $cache_key, 'lbbkr' );
    
        if ( false === $result ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- safe usage with internal table and prepared input
            $result = (bool) $wpdb->get_var(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- custom table is internal and safe
                    "SELECT COUNT(*) FROM `{$this->table}` WHERE ip = %s",
                    $ip
                )
            );

    
            wp_cache_set( $cache_key, $result, 'lbbkr', 300 );

        }
    
        return $result;

    }
    
    
    
    

	public function drop_table() {

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- DROP TABLE used on uninstall/dev only
		$wpdb->query( "DROP TABLE IF EXISTS {$this->table}" );

	}

}
