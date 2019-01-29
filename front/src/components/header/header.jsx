import React from "react";
import './header.scss';

const header = (props) => {
  return (
    <nav className="navbar navbar-light bg-light">
      <a className="navbar-brand" href="#">
        <div class="img-container">
          <img src={require("../../assets/img/logo.svg")} />
        </div>
      </a>
    </nav>
  );
};

export default header;
