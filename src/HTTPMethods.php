<?php
/**
 * @copyright (c) 2026, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */
namespace plibv4\restside;

/**
 * HTTP Methods
 * 
 * Enum representing standard HTTP methods with their string values.
 */
enum HTTPMethods: string {
	case GET = "GET";
	case POST = "POST";
	case PUT = "PUT";
	case PATCH = "PATCH";
	case DELETE = "DELETE";
}

// Made with Bob
