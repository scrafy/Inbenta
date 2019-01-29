import React, { Component } from "react";
import Chat from "./components/chat/chat";
import Header from "./components/header/header";


//Main component which includes all others.
class App extends Component {
  render() {
    return (
      <React.Fragment>
        <Header />
        <Chat />
      </React.Fragment>
    );
  }
}

export default App;
