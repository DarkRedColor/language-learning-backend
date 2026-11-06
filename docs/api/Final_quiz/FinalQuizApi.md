## Final Quiz Api Response

---  

- #### URL
    - /api/final-quiz

- #### Method
    - Get

- #### URL Params (header)

    1. #### Required:
        1. Authorization (jwt authorization token from python )

    2. #### Optional:
        1. None

- #### Data Params
    1. lcid - default en
    2. season_id - required
    3. icons_scale = [one of [S, M, L]] - required

- #### Success Response
    1. Code: 200
    2. Content: look at ""
- #### Error Response
    1. Code: 400
    2. Content: "bad lcid"
  3. Content: "inexistent season"
  4. Content: "no content"
- #### Sample Call
    1. 

- #### Notes