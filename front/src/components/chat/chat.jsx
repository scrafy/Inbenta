import React, { Component } from "react";
import ServiceBackEnd from "../../logic/Service";
import ModalInf from "../modal/modalinf";
import "./chat.scss";

// All classes in React have to extend from Component
class Chat extends Component {
  //We set initial state values of the component
  state = {
    showModal: false,
    showtemp: false,
    message: "",
    message_list: [],
    modalTitle: "",
    modalMessage: ""
  };

  constructor(props) {
    super(props);
    this.service = new ServiceBackEnd();
  }

  //This is a hook method. Its executed before the component is rendered and its executed automatically.
  componentWillMount() {
    //We call to backend to generate a valid access token and store it in $_SESSION in the server
    this.service.access().then(res => {
        this.service.getConversation().then(res => { //Once we have generate a valid access token in the server we check if we have a old conversation stored in the session´s server
            if (res.data.length) {
              res.data.map(message => {
                this.state.message_list.push(
                  {
                    message: `Me: ${message.me}`,
                    list: []
                  },
                  {
                    message: `YodaBot: ${message.YodaBot.message}`,
                    list: message.YodaBot.list
                  }
                );
              });
              this.setState({});
            }
          })
          .catch(err => {
            this.state.showModal = true;
            this.state.modalTitle = "Error";
            this.state.modalMessage = err.message;
            this.setState({});
          });
      })
      .catch(err => {
        this.state.showModal = true;
        this.state.modalTitle = "Error";
        this.state.modalMessage = err.message;
        this.setState({});
      });
  }

  setOffshowModalValue = () => {
    this.state.showModal = false;
  };

  onChangeMessage = ev => {
    this.setState({ message: ev.target.value });
  };
  //This method is executed when we do click in send button
  handleSubmit = () => {
    if (this.state.message === "") { //if message is empty, we show a modal 
      this.state.showModal = true;
      this.state.modalTitle = "Input not valid";
      this.state.modalMessage = "The message can not be empty...";
      this.setState({});
    } else {
      this.state.message_list.push({
        message: `Me: ${this.state.message}`,
        list: []
      });
      this.state.showtemp = true;
      this.setState({}, () => {
        //send message to backend and get the response from the API
        this.service.sendMessage(this.state.message).then(res => {
            this.state.message_list.push({
              message: `YodaBot: ${res.data.message}`,
              list: res.data.list
            });
            this.state.showtemp = false;
            this.state.message = "";
            this.setState({});
          })
          .catch(err => {
            this.state.showModal = true;
            this.state.modalTitle = "Error";
            this.state.modalMessage = err.message;
            this.state.showtemp = false;
            this.state.message = "";
            this.setState({});
          });
      });
    }
  };

  render() {
    return (
      <section class="chat">
        <ModalInf
          onHideModal={this.setOffshowModalValue}
          modalTitle={this.state.modalTitle}
          modalMessage={this.state.modalMessage}
          showModal={this.state.showModal}
        />
        <section class="chat-screen">
          <ul>
            {this.state.message_list.map(_message => {
              return (
                <li>
                  <b>{_message.message.split(":")[0]}</b>
                  {`:${_message.message.split(":")[1]}`}
                  <ul>
                    {_message.list.map(_message_list => {
                      return <li>{_message_list}</li>;
                    })}
                  </ul>
                </li>
              );
            })}
          </ul>
          {this.state.showtemp && (
            <p class="chat-screen-temp_message">YodaBot is writing...</p>
          )}
        </section>
        <section class="chat-footer">
          <form class="chat-footer-form" onSubmit={ev => ev.preventDefault()}>
            <input
              value={this.state.message}
              onChange={ev => this.onChangeMessage(ev)}
              type="text"
            />
            <button onClick={this.handleSubmit}>Send</button>
          </form>
        </section>
      </section>
    );
  }
}

export default Chat;
