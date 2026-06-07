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
		$headers = ['Content-Type' => 'application/json'];
		$queryParams = new ArrayFetch(['page' => '1', 'limit' => '10']);
		
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/users',
			$headers,
			$queryParams,
			''
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
		$headers = ['Content-Type' => 'application/json'];
		$queryParams = new ArrayFetch([]);
		$body = '{"name":"John","email":"john@example.com"}';
		
		$request = HTTPRequest::fromManual(
			HTTPMethods::POST,
			'/api/users',
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
		$request = HTTPRequest::fromManual(
			HTTPMethods::PUT,
			'/api/users/1',
			[],
			new ArrayFetch([]),
			''
		);
		
		$this->assertEquals(HTTPMethods::PUT, $request->getMethod());
	}
	
	/**
	 * Test get URI
	 */
	function testGetUri(): void {
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/products?category=electronics',
			[],
			new ArrayFetch([]),
			''
		);
		
		$this->assertEquals('/api/products?category=electronics', $request->getUri());
	}
	
	/**
	 * Test get headers
	 */
	function testGetHeaders(): void {
		$headers = [
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer token123'
		];
		
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/users',
			$headers,
			new ArrayFetch([]),
			''
		);
		
		$this->assertEquals($headers, $request->getHeaders());
	}
	
	/**
	 * Test has header returns true
	 */
	function testHasHeaderTrue(): void {
		$headers = ['Content-Type' => 'application/json'];
		
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/users',
			$headers,
			new ArrayFetch([]),
			''
		);
		
		$this->assertTrue($request->hasHeader('Content-Type'));
	}
	
	/**
	 * Test has header returns false
	 */
	function testHasHeaderFalse(): void {
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/users',
			[],
			new ArrayFetch([]),
			''
		);
		
		$this->assertFalse($request->hasHeader('Authorization'));
	}
	
	/**
	 * Test get header
	 */
	function testGetHeader(): void {
		$headers = ['Content-Type' => 'application/json'];
		
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/users',
			$headers,
			new ArrayFetch([]),
			''
		);
		
		$this->assertEquals('application/json', $request->getHeader('Content-Type'));
	}
	
	/**
	 * Test get header throws exception when not found
	 */
	function testGetHeaderNotFound(): void {
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/users',
			[],
			new ArrayFetch([]),
			''
		);
		
		$this->expectException(OutOfBoundsException::class);
		$this->expectExceptionMessage("Header 'Authorization' not found");
		$request->getHeader('Authorization');
	}
	
	/**
	 * Test get query params returns ArrayFetch
	 */
	function testGetQueryParams(): void {
		$queryParams = new ArrayFetch(['page' => '1', 'limit' => '10']);
		
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/users',
			[],
			$queryParams,
			''
		);
		
		$this->assertInstanceOf(ArrayFetch::class, $request->getQueryParams());
	}
	
	/**
	 * Test get body
	 */
	function testGetBody(): void {
		$body = '{"name":"Jane","email":"jane@example.com"}';
		
		$request = HTTPRequest::fromManual(
			HTTPMethods::POST,
			'/api/users',
			[],
			new ArrayFetch([]),
			$body
		);
		
		$this->assertEquals($body, $request->getBody());
	}
	
	/**
	 * Test get empty body
	 */
	function testGetEmptyBody(): void {
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/users',
			[],
			new ArrayFetch([]),
			''
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
		
		foreach($methods as $method) {
			$request = HTTPRequest::fromManual(
				$method,
				'/api/resource',
				[],
				new ArrayFetch([]),
				''
			);
			
			$this->assertEquals($method, $request->getMethod());
		}
	}
	
	/**
	 * Test get request path with simple path
	 */
	function testGetRequestPathSimple(): void {
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/users',
			[],
			new ArrayFetch([]),
			''
		);
		
		$this->assertEquals('/api/users', $request->getRequestPath());
	}
	
	/**
	 * Test get request path with query string
	 */
	function testGetRequestPathWithQueryString(): void {
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/products?category=electronics&page=1',
			[],
			new ArrayFetch([]),
			''
		);
		
		$this->assertEquals('/api/products', $request->getRequestPath());
	}
	
	/**
	 * Test get request path with root path
	 */
	function testGetRequestPathRoot(): void {
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/',
			[],
			new ArrayFetch([]),
			''
		);
		
		$this->assertEquals('/', $request->getRequestPath());
	}
	
	/**
	 * Test get request path with nested path
	 */
	function testGetRequestPathNested(): void {
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/v1/users/123/profile',
			[],
			new ArrayFetch([]),
			''
		);
		
		$this->assertEquals('/api/v1/users/123/profile', $request->getRequestPath());
	}
	
	/**
	 * Test get request path with query string and fragment
	 */
	function testGetRequestPathWithQueryAndFragment(): void {
		$request = HTTPRequest::fromManual(
			HTTPMethods::GET,
			'/api/users?page=1#section',
			[],
			new ArrayFetch([]),
			''
		);
		
		$this->assertEquals('/api/users', $request->getRequestPath());
	}
}

// Made with Bob
