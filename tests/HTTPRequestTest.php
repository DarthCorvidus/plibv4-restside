<?php
/**
 * @copyright (c) 2026, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license LGPL
 */

declare(strict_types=1);

namespace plibv4\restside;
use PHPUnit\Framework\TestCase;
use plibv4\import\ArrayFetch;
use OutOfBoundsException;

/** @psalm-suppress UnusedClass */
final class HTTPRequestTest extends TestCase {
	/**
	 * Test create from manual with GET method
	 */
	function testFromManualGet(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/users';
		$headers = ['Content-Type' => 'application/json'];
		$queryParams = new ArrayFetch(['page' => '1', 'limit' => '10']);
		$requestBody = '';

		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertInstanceOf(HTTPRequest::class, $request);
		$this->assertEquals(HTTPMethods::GET, $request->getMethod());
		$this->assertEquals('/api/users', $request->getUri());
		$this->assertEquals('', $request->getBody());
	}
	
	/**
	 * Test create from manual with POST method and body
	 */
	function testFromManualPost(): void {
		$method = HTTPMethods::POST;
		$requestUri = '/api/users';
		$headers = ['Content-Type' => 'application/json'];
		$queryParams = new ArrayFetch([]);
		$body = '{"name":"John","email":"john@example.com"}';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$body
		);
		
		$this->assertEquals(HTTPMethods::POST, $request->getMethod());
		$this->assertEquals('/api/users', $request->getUri());
		$this->assertEquals($body, $request->getBody());
	}
	
	/**
	 * Test get method
	 */
	function testGetMethod(): void {
		$method = HTTPMethods::PUT;
		$requestUri = '/api/users/1';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertEquals(HTTPMethods::PUT, $request->getMethod());
	}
	
	/**
	 * Test get URI
	 */
	function testGetUri(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/products?category=electronics';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertEquals('/api/products?category=electronics', $request->getUri());
	}
	
	/**
	 * Test get headers
	 */
	function testGetHeaders(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/users';
		$headers = [
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer token123'
		];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertEquals($headers, $request->getHeaders());
	}
	
	/**
	 * Test has header returns true
	 */
	function testHasHeaderTrue(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/users';
		$headers = ['Content-Type' => 'application/json'];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertTrue($request->hasHeader('Content-Type'));
	}
	
	/**
	 * Test has header returns false
	 */
	function testHasHeaderFalse(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/users';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertFalse($request->hasHeader('Authorization'));
	}
	
	/**
	 * Test get header
	 */
	function testGetHeader(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/users';
		$headers = ['Content-Type' => 'application/json'];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertEquals('application/json', $request->getHeader('Content-Type'));
	}
	
	/**
	 * Test get header throws exception when not found
	 */
	function testGetHeaderNotFound(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/users';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->expectException(OutOfBoundsException::class);
		$this->expectExceptionMessage("Header 'Authorization' not found");
		$request->getHeader('Authorization');
	}
	
	/**
	 * Test get query params returns ArrayFetch
	 */
	function testGetQueryParams(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/users';
		$headers = [];
		$queryParams = new ArrayFetch(['page' => '1', 'limit' => '10']);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertInstanceOf(ArrayFetch::class, $request->getQueryParams());
	}
	
	/**
	 * Test get body
	 */
	function testGetBody(): void {
		$method = HTTPMethods::POST;
		$requestUri = '/api/users';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$body = '{"name":"Jane","email":"jane@example.com"}';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$body
		);
		
		$this->assertEquals($body, $request->getBody());
	}
	
	/**
	 * Test get empty body
	 */
	function testGetEmptyBody(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/users';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertEquals('', $request->getBody());
	}
	
	/**
	 * Test all HTTP methods
	 */
	function testAllHttpMethods(): void {
		$methods = [
			HTTPMethods::GET,
			HTTPMethods::POST,
			HTTPMethods::PUT,
			HTTPMethods::PATCH,
			HTTPMethods::DELETE
		];
		$requestUri = '/api/resource';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		foreach($methods as $method) {
			$request = HTTPRequest::fromManual(
				$method,
				$requestUri,
				$headers,
				$queryParams,
				$requestBody
			);
			
			$this->assertEquals($method, $request->getMethod());
		}
	}
	
	/**
	 * Test get request path with simple path
	 */
	function testGetRequestPathSimple(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/users';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertEquals('/api/users', $request->getRequestPath());
	}
	
	/**
	 * Test get request path with query string
	 */
	function testGetRequestPathWithQueryString(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/products?category=electronics&page=1';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertEquals('/api/products', $request->getRequestPath());
	}
	
	/**
	 * Test get request path with root path
	 */
	function testGetRequestPathRoot(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertEquals('/', $request->getRequestPath());
	}
	
	/**
	 * Test get request path with nested path
	 */
	function testGetRequestPathNested(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/v1/users/123/profile';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertEquals('/api/v1/users/123/profile', $request->getRequestPath());
	}
	
	/**
	 * Test get request path with query string and fragment
	 */
	function testGetRequestPathWithQueryAndFragment(): void {
		$method = HTTPMethods::GET;
		$requestUri = '/api/users?page=1#section';
		$headers = [];
		$queryParams = new ArrayFetch([]);
		$requestBody = '';
		
		$request = HTTPRequest::fromManual(
			$method,
			$requestUri,
			$headers,
			$queryParams,
			$requestBody
		);
		
		$this->assertEquals('/api/users', $request->getRequestPath());
	}
}

// Made with Bob
