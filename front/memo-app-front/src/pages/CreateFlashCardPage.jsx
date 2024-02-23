import React, {useState} from 'react';
import styled from 'styled-components';
import {API_URL} from '../helpers/getApiUrl';


const StyledPage = styled.div`
    display: flex;
    flex-direction: column;
    background-color: #fff;
    padding: 3em;
    border-radius: 3em;
    margin: 3em;

    h2, h3, p, input, label {
        color: #000;
    }

    button {
    background-color: #000;
    color: #fff;
    border: none;
    padding: 10px;
    border-radius: 10px;
    margin: 10px;
    cursor: pointer;
}

`;


export const CreateFlashCardPage = () => {
    const [question, setQuestion] = useState('');
    const [answer, setAnswer] = useState('');
    const [tag, setTag] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();

        try {
            const cardData = {question, answer, tag};
            const response = await fetch(`${API_URL}/cards`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(cardData),
            });

            if (response.status === 201) {
                const responseData = await response.json();
                console.log('Card Created:', responseData);
                // Reset form or redirect user
                setQuestion('');
                setAnswer('');
                alert('Card created successfully!');
            } else {
                console.error('Failed to create the card. Status:', response.status);
                alert('Failed to create the card.');
                setError('Failed to create the card.')
            }
        } catch (error) {
            console.error('Error creating card:', error);
            alert('Error creating the card. Please try again later.');
        }
    };

    return (
        <StyledPage>
            <h2>Create a New Flash Card</h2>
            <form onSubmit={handleSubmit} data-testid="create-card-form"
            >
                <div>
                    <label htmlFor="question">Question:</label>
                    <input data-testid="question-input"
                           type="text"
                           id="question"
                           value={question}
                           onChange={(e) => setQuestion(e.target.value)}
                           required
                    />
                </div>
                <div>
                    <label htmlFor="answer">Answer:</label>
                    <input
                        data-testid="answer-input"
                        type="text"
                        id="answer"
                        value={answer}
                        onChange={(e) => setAnswer(e.target.value)}
                        required
                    />
                </div>
                <div>
                    <label htmlFor="tag"
                           data-testid="tag-input-label"
                    >
                        Tag
                    </label>
                    <input
                        data-testid="tag-input"

                        type="text"
                        id="tag"
                        value={tag}
                        onChange={(e) => setTag(e.target.value)}
                        required
                    />
                </div>
                <button type="submit">Create Card</button>
                {error && <p>{error}</p>}
            </form>
        </StyledPage>
    );
};

export default CreateFlashCardPage;
