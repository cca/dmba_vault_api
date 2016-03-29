# VAULT API for Design MBA Program

A wrapper around the EQUELLA (software that runs VAULT) API for the Design MBA program. Allows someone to interact with the files and metadata related to DMBA in VAULT without knowing the internal workings all that well. Live demo at http://libraries.cca.edu/dmba/

Uses [Composer](https://getcomposer.org/) to manage the [Guzzle](https://guzzle3.readthedocs.org/http-client/client.html) HTTP library dependency. Run `composer install` to get set up.

## Request Parameters

By default, the API just executes a search of the DMBA collection with all the default settings and returns a list of 10 items. Here are some parameters one can alter to obtain different results:

- **semester**: limit the semester results are from, semesters are of form "(Spring|Fall|Summer) YYYY" e.g. `Spring 2015`
- **length**: default `10`, number of results to return
- **q**: free text query to execute, e.g. "venture financing"
- **start**: default `0`, number of the first search result to return
- **order**: ordering principle of the result list, defaults to VAULT's internal relevance ranking but can also be set to `modified` (date last modified) or `name` (alphabetical by name)
- **reverse**: default `false`, whether results should be listed in reverse, set to `true` to override

There is also a **debug** parameter which, when set to any value, causes the app to return the EQUELLA API response instead of its modified response. Useful for development purposes but probably not for API clients.

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

## License

[Apache 2.0](https://www.apache.org/licenses/LICENSE-2.0)
