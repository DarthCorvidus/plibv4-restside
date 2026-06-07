<?php
/**
 * @copyright (c) 2026, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */
namespace plibv4\restside;
use OutOfBoundsException;
use RuntimeException;
use plibv4\import\ArrayFetch;

/**
 * HTTP Request
 * 
 * Encapsulates HTTP request data as sent to the server, providing a clean
 * interface to access request method, URI, headers, query parameters, and body.
 */
final class HTTPRequest {
	private HTTPMethods $method;
	private string $uri;
	/** @var array<string, string> */
	private array $headers;
	private ArrayFetch $queryParams;
	private string $body;
	
	/**
	 * Constructor
	 *
	 * Private constructor to enforce use of factory methods.
	 *
	 * @param HTTPMethods $method
	 * @param string $uri
	 * @param array<string, string> $headers
	 * @param ArrayFetch $queryParams
	 * @param string $body
	 */
	private function __construct(HTTPMethods $method, string $uri, array $headers, ArrayFetch $queryParams, string $body) {
		$this->method = $method;
		$this->uri = $uri;
		$this->headers = $headers;
		$this->queryParams = $queryParams;
		$this->body = $body;
	}
	
	/**
	 * From Manual
	 *
	 * Creates an HTTPRequest instance from manually provided parameters.
	 *
	 * @param HTTPMethods $method
	 * @param string $uri
	 * @param array<string, string> $headers
	 * @param ArrayFetch $queryParams
	 * @param string $body
	 * @return HTTPRequest
	 */
	static function fromManual(HTTPMethods $method, string $uri, array $headers, ArrayFetch $queryParams, string $body): HTTPRequest {
		return new HTTPRequest($method, $uri, $headers, $queryParams, $body);
	}
	
	/**
	 * From Request
	 *
	 * Creates an HTTPRequest instance from PHP's native request superglobals
	 * and functions.
	 *
	 * @return HTTPRequest
	 * @throws RuntimeException if REQUEST_METHOD is not set or invalid
	 */
	static function fromRequest(): HTTPRequest {
		if(!isset($_SERVER['REQUEST_METHOD'])) {
			throw new RuntimeException("REQUEST_METHOD not set in \$_SERVER");
		}
		
		try {
			$method = HTTPMethods::from($_SERVER['REQUEST_METHOD']);
		} catch(\ValueError $e) {
			throw new RuntimeException("Invalid REQUEST_METHOD: {$_SERVER['REQUEST_METHOD']}", 0, $e);
		}
		
		$uri = $_SERVER['REQUEST_URI'] ?? '/';
		$headers = self::getRequestHeaders();
		$queryParams = new ArrayFetch($_GET);
		
		// Only read body for methods that support it
		$body = '';
		if($method === HTTPMethods::POST || $method === HTTPMethods::PUT || $method === HTTPMethods::PATCH) {
			$bodyContent = file_get_contents('php://input');
			$body = $bodyContent !== false ? $bodyContent : '';
		}
		
		return new HTTPRequest($method, $uri, $headers, $queryParams, $body);
	}
	
	/**
	 * Get Request Headers
	 * 
	 * Retrieves all HTTP request headers using getallheaders() if available,
	 * or falls back to parsing $_SERVER.
	 * 
	 * @return array<string, string>
	 */
	private static function getRequestHeaders(): array {
		if(function_exists('getallheaders')) {
			/** @var array<string, string>|false */
			$headers = getallheaders();
			return $headers !== false ? $headers : [];
		}
		
		$headers = [];
		foreach($_SERVER as $key => $value) {
			if(!is_string($value)) {
				continue;
			}
			if(str_starts_with($key, 'HTTP_')) {
				$header = str_replace('_', '-', substr($key, 5));
				$header = ucwords(strtolower($header), '-');
				$headers[$header] = $value;
			}
		}
		return $headers;
	}
	
	/**
	 * Get Method
	 * 
	 * Returns the HTTP method of the request.
	 * 
	 * @return HTTPMethods
	 */
	function getMethod(): HTTPMethods {
		return $this->method;
	}
	
	/**
	 * Get URI
	 * 
	 * Returns the request URI.
	 * 
	 * @return string
	 */
	function getUri(): string {
		return $this->uri;
	}
	
	/**
	 * Get Headers
	 * 
	 * Returns all request headers.
	 * 
	 * @return array<string, string>
	 */
	function getHeaders(): array {
		return $this->headers;
	}
	
	/**
	 * Has Header
	 *
	 * Checks if a specific header is present.
	 *
	 * @param string $name
	 * @return bool
	 */
	function hasHeader(string $name): bool {
		return isset($this->headers[$name]);
	}
	
	/**
	 * Get Header
	 *
	 * Returns a specific header value.
	 *
	 * @param string $name
	 * @return string
	 * @throws OutOfBoundsException if header is not present
	 */
	function getHeader(string $name): string {
		if(!$this->hasHeader($name)) {
			throw new OutOfBoundsException("Header '{$name}' not found");
		}
		return $this->headers[$name];
	}
	
	/**
	 * Get Query Params
	 *
	 * Returns the ArrayFetch instance for query parameters.
	 *
	 * @return ArrayFetch
	 */
	function getQueryParams(): ArrayFetch {
		return $this->queryParams;
	}
	
	/**
	 * Get Body
	 * 
	 * Returns the raw request body.
	 * 
	 * @return string
	 */
	function getBody(): string {
		return $this->body;
	}
}

// Made with Bob
