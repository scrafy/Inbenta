require("isomorphic-fetch");

//This class is the responsable to communicate with the API.
//Instances of this class are used by React components.
class ServiceBackEnd {
  constructor() {
    this.endpoint = process.env.REACT_APP_API_ENDPOINT;
  }

  setEnPoint(url) {
    this.endpoint = url;
  }

  //This method call to API to get a valid access token and it returns a promise
  access() {
    return fetch(`${this.endpoint}/access`, {
      method: "GET",
      headers: {
        Accept: "application/json"
      },
      credentials: "include"
    })
      .then(res => res.json())
      .then(res => {
        if (res.status === "OK") return res;

        throw new Error(res.err_description);
      });
  }

  //This method call to API to send a message and it returns a promise
  sendMessage(message) {
    return fetch(`${this.endpoint}/message?message=${message}`, {
      method: "GET",
      headers: {
        Accept: "application/json"
      },
      credentials: "include"
    })
      .then(res => res.json())
      .then(res => {
        if (res.status === "OK") return res;

        throw new Error(res.err_description);
      });
  }

  //This method call to API to get an old conversation stored in the session´s server. Its called when the webpace is reloaded and it returns a promise
  getConversation() {
    return fetch(`${this.endpoint}/history`, {
      method: "GET",
      headers: {
        Accept: "application/json"
      },
      credentials: "include"
    })
      .then(res => res.json())
      .then(res => {
        if (res.status === "OK") return res;

        throw new Error(res.err_description);
      });
  }
}

export default ServiceBackEnd;
