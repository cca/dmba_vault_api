# VAULT API for Design MBA Program

A wrapper around the VAULT API for the Design MBA program. Allows someone to interact with the DMBA materials in VAULT without knowing the internal workings of the system or our metadata schema. Live endpoint at http://libraries.cca.edu/dmba/

Uses [Composer](https://getcomposer.org/) to manage the [Guzzle](https://guzzle3.readthedocs.org/http-client/client.html) HTTP library dependency. To get set up:

- run `composer install` to get dependencies
- copy "collections.example.php" to "collections.php" & change it to the desired list of collect UUIDs

You can test that it's working with `php -S 8000` which will run a local PHP server on port 8000.

## Request Parameters

By default, the API just executes a search of the DMBA collection with all the default settings and returns a list of 10 items. Here are parameters that one can alter to obtain different results:

- **semester**: limit the semester results are from, semesters are of form "(Spring|Fall|Summer) YYYY" e.g. `Spring 2015`
- **q**: free text query over all metadata fields, e.g. "venture financing"
- **id**: return _only_ the item with a specific ID (_this option ignores the 2 parameters above_)
- **length**: default `10`, number of results to return (_maximum 50_)
- **start**: default `0`, number of the first search result to return
- **order**: ordering principle of the result list, defaults to VAULT's internal relevance ranking but can also be set to `modified` (date last modified) or `name` (alphabetical by name)
- **reverse**: default `false`, whether results should be listed in reverse, set to `true` to override

There is also a **debug** parameter which, when set to any value, causes the app to return the EQUELLA API response instead of its modified response. Useful for development purposes but probably not for API clients.

## Invalid Queries

Invalid queries will result in a HTTP 400 response with the following JSON structure:

```js
{
    "errors" : [
        "first error message",
        "second error message"
    ]
}
```

The error messages should make it clear which parameters were used improperly. Consult the section above to determine how to fix the error(s).

## Field Definitions & Information

For example JSON, see the section below and the "response.json" sample in this project.

All API results return an object with just two properties, `vault_api_url` and `results`. The `vault_api_url` is a link back to the VAULT application's API (of which this API is an abstraction); client applications should not need to refer to it, it is present merely for troubleshooting purposes. The `results` property is an array of items matching the query parameters (see the _Request Parameters_ section above for details on parameters).

Each item within the `results` array has the following properties:

- a unique `id` string in [standard UUID format](https://en.wikipedia.org/wiki/Universally_unique_identifier)
- a `name` string which is the title of the project
- a longer `description` string which is in plain text but often a few paragraphs long, containing line breaks but no text formatting
- a `link` URL for the item's summary page in VAULT
- an `attachments` array that describes files associated with the item (see below for details on the properties of an attachment)
- a `students` string which is a comma-separated list of student names
- a `semester` string of format "(Spring|Summer|Fall) YYYY" where "YYYY" is the four-digit year
- a `course` string which is the course title
- a `faculty` string of comma-separated instructors' names
- a `section` string which is the course section code, of a format matching the regular expression `[A-Z]{5}-[0-9]{3}-[A-Z0-9]{2}` e.g. "DSMBA-404-1A"
    + Note that there are multiple _sections_ of the same _course_, e.g. the DSMBA-608-1A and DMSBA-608-1B _sections_ are both instances of the _course_ "Venture Studio"
- a `courseName` string which is merely the first 9 characters of the `section`, e.g. DSMBA-608 (not present in all items)
- a `facultyID` string which is a comma-separated list of the instructors' CCA usernames (not present in all items)
- a `XList` string which is a numeric identifier linking "cross-listed" courses that appear under multiple programs (not present in all items)

Each attachment within the `attachments` array has many properties, most of which are only important to VAULT itself and of dubious relevance to API clients. The properties which are most likely to be useful are:

- a `type` string which is either "file" or "url" (meaning it's a URL of a resource not hosted in the VAULT archive)
    + note that `type: "url"` attachments will not have the fields listed below but will have a `url` property that can be used to generate a link
- a `filename` string which can be parsed for file extension (e.g. ".pdf") to get a clue as to what type of file the attachment is
- a `size` integer which is the file size in bytes
- a `links` hash with two string properties, `thumbnail` and `view` which respectively point to a small, system-generated thumbnail and the file itself
    + thumbnails are 88x66 300dpi JPG images
    + by appending a "?gallery=preview" query string to the thumbnail URL, one can obtain a larger thumbnail that's up to 500px in one dimension by varies in aspect ratio depending on the dimensions of the original image

Almost all of the other properties of attachments are uninformative, though it's perhaps worth noting that one can construct the thumbnail and resource URLs for a file given the parent item's ID and the file's `uuid` property.

## Sample Response

See also example.html in this folder, which shows how a JavaScript client might use the API. When I perform a `GET http://libraries.cca.edu/dmba/?q=money&semester=Spring+2015` HTTP request I see a sightly longer version of the following (I truncated the descriptions and removed results):

```js
{
    "vault_api_url": "https://vault.cca.edu/api/search?q=money&info=metadata%2Cbasic%2Cattachment&collections=70a86791-8453-4ad3-9906-f4e070621d05&where=%2Fxml%2Flocal%2FcourseInfo%2Fsemester%20%3D%20%27Spring%202015%27",
    "results": [{
        "id": "1c1490b9-83e0-4c18-bc24-240674785048",
        "name": "Venture Financing Final Presentation and Paper",
        "description": "It takes money to make money. Businesses need varying amounts of funds…",
        "link": "https://vault.cca.edu/items/1c1490b9-83e0-4c18-bc24-240674785048/1/",
        "attachments": [
            {
                "type": "file",
                "uuid": "6d1140e1-d0cc-453b-8f8a-34712a7582c5",
                "description": "R5 Final Deck_Venture Financing.pdf",
                "viewer": "",
                "preview": false,
                "restricted": false,
                "filename": "R5 Final Deck_Venture Financing.pdf",
                "size": 4641263,
                "thumbFilename": "_THUMBS/R5 Final Deck_Venture Financing.pdf.jpeg",
                "conversion": false,
                "links": {
                    "thumbnail": "https://vault.cca.edu/thumbs/1c1490b9-83e0-4c18-bc24-240674785048/1/6d1140e1-d0cc-453b-8f8a-34712a7582c5",
                    "view": "https://vault.cca.edu/items/1c1490b9-83e0-4c18-bc24-240674785048/1/?attachment.uuid=6d1140e1-d0cc-453b-8f8a-34712a7582c5"
                }
            }, {
                "type": "file",
                "uuid": "e29fb566-b71d-4a95-8ba3-093edd94b523",
                "description": "Venture Financing-spreads-v4b.pdf",
                "viewer": "",
                "preview": false,
                "restricted": false,
                "filename": "Venture Financing-spreads-v4b.pdf",
                "size": 2072268,
                "thumbFilename": "_THUMBS/Venture Financing-spreads-v4b.pdf.jpeg",
                "conversion": false,
                "links": {
                    "thumbnail": "https://vault.cca.edu/thumbs/1c1490b9-83e0-4c18-bc24-240674785048/1/e29fb566-b71d-4a95-8ba3-093edd94b523",
                    "view": "https://vault.cca.edu/items/1c1490b9-83e0-4c18-bc24-240674785048/1/?attachment.uuid=e29fb566-b71d-4a95-8ba3-093edd94b523"
                }
            }
        ],
        "students": "Doe Johnson, Antonio Jackson,  Cerberus Hu, Xiu Zao, Mikhail Garbanzo ",
        "semester": "Spring 2015",
        "course": "Money Strategies",
        "faculty": "Steven Gilman",
        "section": "DSMBA-632-1B"
    }, {
        "id": "df14f1bb-39ae-4e45-84e3-6ce9dafa2542",
        "name": "GIFTD",
        "description": "GIFTD is a service that tailors great gift ideas to take the stress…",
        "link": "https://vault.cca.edu/items/df14f1bb-39ae-4e45-84e3-6ce9dafa2542/1/",
        "attachments": [
            {
                "type": "file",
                "uuid": "e49e78b8-b7db-43fa-8f98-39dcf682c2a0",
                "description": "LOGO Gifted Final.pdf",
                "viewer": "",
                "preview": false,
                "restricted": false,
                "filename": "LOGO Gifted Final.pdf",
                "size": 9923,
                "thumbFilename": "_THUMBS/LOGO Gifted Final.pdf.jpeg",
                "conversion": false,
                "links": {
                    "thumbnail": "https://vault.cca.edu/thumbs/df14f1bb-39ae-4e45-84e3-6ce9dafa2542/1/e49e78b8-b7db-43fa-8f98-39dcf682c2a0",
                    "view": "https://vault.cca.edu/items/df14f1bb-39ae-4e45-84e3-6ce9dafa2542/1/?attachment.uuid=e49e78b8-b7db-43fa-8f98-39dcf682c2a0"
                }
            }, {
                "type": "file",
                "uuid": "289407a9-86d4-4d2f-83af-2a191aa20015",
                "description": "LOGO Gifted Final(2).pdf",
                "viewer": "",
                "preview": false,
                "restricted": false,
                "filename": "LOGO Gifted Final(2).pdf",
                "size": 9923,
                "thumbFilename": "_THUMBS/LOGO Gifted Final(2).pdf.jpeg",
                "conversion": false,
                "links": {
                    "thumbnail": "https://vault.cca.edu/thumbs/df14f1bb-39ae-4e45-84e3-6ce9dafa2542/1/289407a9-86d4-4d2f-83af-2a191aa20015",
                    "view": "https://vault.cca.edu/items/df14f1bb-39ae-4e45-84e3-6ce9dafa2542/1/?attachment.uuid=289407a9-86d4-4d2f-83af-2a191aa20015"
                }
            }
        ],
        "students": "Ham Danish, Etsy Flourish, Dougie Wallace",
        "semester": "Spring 2015",
        "course": "Venture Studio",
        "faculty": "Robert Neher",
        "section": "DSMBA-608-2A"
    }]
}
```

Note that a request with an `id` parameter will return JSON as formatted above, but there is guaranteed to be only one item in the `results` array.

## License

[Apache 2.0](https://www.apache.org/licenses/LICENSE-2.0)
