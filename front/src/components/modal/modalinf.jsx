import { Modal, ModalHeader, ModalBody, ModalFooter } from 'reactstrap';
import React from 'react';


//This component represents a modal inf. We can set title and body message. It´s reusable accross the app
class ModalInf extends React.Component {

    state = {}

    componentWillReceiveProps(props) {

        this.setState({ showModal: props.showModal, message: props.modalMessage, title: props.modalTitle })
    }

    toggle = () => {

        this.state.showModal = !this.state.showModal
        this.setState({})
        this.props.onHideModal()
    }

    render() {
        return (
            <div>
                <Modal isOpen={this.state.showModal} toggle={this.toggle}>
                    <ModalHeader toggle={this.toggle}>{this.state.title}</ModalHeader>
                    <ModalBody>
                        <section>
                            <p>{this.state.message}</p>
                        </section>
                    </ModalBody>
                    <ModalFooter>
                        <button onClick={this.toggle}>Close</button>
                    </ModalFooter>
                </Modal>
            </div >

        );
    }
}

export default ModalInf