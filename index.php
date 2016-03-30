<?php
// Guzzle v3 — our PHP is too old to do latest version
// https://guzzle3.readthedocs.org/docs.html
require 'vendor/autoload.php';
use Guzzle\Http\Client;
require 'validate.php';

// constants
define('VAULT_URL', 'https://vault.cca.edu');
define('SEARCH_API', '/api/search');
// this is the UUID for the Design Strategy (MBA) Program collection
define('COLLECTION_IDS', '70a86791-8453-4ad3-9906-f4e070621d05');

// HTTP headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
// enable CORS
header('Access-Control-Allow-Origin: *');
// no reason to broadcast our PHP version
header_remove('X-Powered-By');

// construct query string for EQUELLA search API
// parse_str parses a query string into variables inside 2nd array parameter
parse_str($_SERVER['QUERY_STRING'], $options);
$options['info'] = 'metadata,basic,attachment';
// search only specific collections, "all" here would search everything
$options['collections'] = COLLECTION_IDS;

$errors = validate($options);
if ($errors) {
    // indicate HTTP error
    http_response_code(400);

    echo json_encode(array("errors" => $errors));
    exit;
}

// initialize HTTP client
$client = new Client(VAULT_URL, Array(
    // disable SSL validation since our cert causes an error
    'ssl.certificate_authority' => false
));
// @TODO any HTTP headers we need to specify here?

// translate query parameters for EQUELLA

// translate "semester" parameter into XML where query
if (isset($options['semester'])) {
    $semester = $options['semester'];
    unset($options['semester']);
    $options['where'] = '/xml/local/courseInfo/semester = \'' . $semester . '\'';
}

// translate "id" parameter into XML where query
// ignore all other query parameters
if (isset($options['id'])) {
    $id = $options['id'];
    unset($options['id']);
    // ignore any prior `where` or free text queries, ID specificity trumps all else
    unset($options['where']);
    unset($options['q']);
    $options['where'] = '/xml/mods/recordInfo/recordIdentifier = \'' . $id . '\'';
}

// ignore "debug" which will return EQUELLA API response
if (isset($options['debug'])) {
    $debug = true;
    unset($options['debug']);
} else {
    $debug = false;
}

$query_string = http_build_query($options);

// request URL
$request = $client->get(SEARCH_API . '?' . $query_string);

// get JSON from EQUELLA API
$response = $request->send();
$data = $response->json();
$output = Array(
    // useful debugging information
    'vault_api_url' => $request->getUrl(),
    'results' => Array()
);

// iterate over item metadata XML, parsing out
foreach ($data['results'] as $item) {
    // basic info contained in API response
    $output_item = Array(
        'id' => $item['uuid'],
        'name' => $item['name'],
        'description' => $item['description'],
        'link' => $item['links']['view'],
        'attachments' => $item['attachments']
    );

    // grab information from metadata
    $metadata = simplexml_load_string($item['metadata']);
    // remove noisy local/courseInfo/courseinfo node, taxonomy string
    unset($metadata->local->courseInfo->courseinfo);
    // add students to metadata output, only piece outside local/courseInfo we need
    // have to cast to string b/c PHP's XML interface is awkward
    $output_item['students'] = (string) $metadata->local->courseWorkWrapper->groupMembers;
    // merge two data sources & append to our ouput
    // cast $metadata SimpleXMLElement to array
    $output['results'][] = array_merge($output_item, (array) $metadata->local->courseInfo);
}

if ($debug) {
    // send the raw EQUELLA response
    echo $response->getBody();
} else {
    // our manicured subset of EQUELLA's API response with course metadata
    echo json_encode($output);
}
