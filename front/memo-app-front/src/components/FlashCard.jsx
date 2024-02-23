import {useState} from 'react';

import styled from 'styled-components';

import {API_URL} from '../helpers/getApiUrl';

const StyledFlashCard = styled.div`
    display: flex;
    flex-direction: column;
    background-color: #fff;
    padding: 3em;
    border-radius: 3em;
    min-width: 50vw;
    box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.2);

    h2,
    p {
        margin: 10px;
        font-size: 20px;
        color: #000;
    }
`;

const StyledFace = styled.div`
    display: flex;
    justify-content: space-between;
    flex-direction: column;
    margin: 10px;
    align-items: center;
    justify-content: center;
    font-weight: bold;

    h2,
    p,
    h1 {
        margin: 10px;
        font-size: 20px;
        color: #000;
    }
`;

const StyledLineAction = styled.div`
  display: flex;
  justify-content: space-between;
  align-items: center;
`;


export const FlashCard = ({cards = [], initialCardIndex = 0, onCorrect}) => {
    const [face, setFace] = useState('recto');
    const [cardIndex, setCardIndex] = useState(0);
    const [isActiveAnswerCorrect, setIsActiveAnswerCorrect] = useState(false);


    const handleNext = () => {
        if (cardIndex < cards.length - 1) {
            setCardIndex(cardIndex + 1);
            setFace('recto');
        }
    };


    const postAnswer = async (correct) => {
        const card = cards[cardIndex];

        await fetch(`${API_URL}/cards/` + card.id + '/answer', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/merge-patch+json',
            },
            body: JSON.stringify({
                isValid: correct,
            }),
        });
    };

    const handleCorrect = (correct) => {
        if (face === 'recto') {
            setFace('verso');
            setIsActiveAnswerCorrect(correct);
            postAnswer(correct);
        } else {
            setFace('recto');
            postAnswer(correct);
            handleNext();
        }
    };

    const Recto = ({question}) => {
        return (
            <StyledFace>
                <h1 className={'p-40'}>{question}</h1>
                <StyledLineAction>

                    <button  style={{margin: '2em'}} onClick={() => handleCorrect(true)}
                            className="relative inline-flex items-center justify-center p-0.5 mb-2 me-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-red-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
<span
    className="relative px-5 py-2.5 transition-all ease-in duration-75 bg-white text-red-500 rounded-md group-hover:bg-opacity-0">
                  Je ne connais pas la réponse
</span>
                    </button>
                    <button data-testid='know-answer' style={{margin: '2em'}} onClick={() => handleCorrect(false)}
                            className="relative inline-flex items-center justify-center p-0.5 mb-2 me-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-green-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">
<span
    className="relative px-5 py-2.5 transition-all ease-in duration-75 bg-white text-green-500 rounded-md group-hover:bg-opacity-0">
                  Je connais la réponse
</span>
                    </button>
                </StyledLineAction>
            </StyledFace>
        );
    };

    const Verso = ({answer}) => {
        return (
            <StyledFace>
                <h1 className={'p-40'}>{answer}
                </h1>
                <StyledLineAction>
                    {!isActiveAnswerCorrect && (
                        <button data-testid={'force-validation'} onClick={() => handleCorrect(true)}
                                className="relative inline-flex items-center justify-center p-0.5 mb-2 me-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-red-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white dark:text-white focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">

                       <span
                           className="relative px-5 py-2.5 transition-all ease-in duration-75 bg-white text-green-500 rounded-md group-hover:bg-opacity-0">
                  Je ne connais pas la réponse finalement
</span></button>
                    )}
                    <button data-testid={'next'} onClick={() => handleCorrect(false)}
                            className="relative inline-flex items-center justify-center p-0.5 mb-2 me-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-red-500 to-blue-500 group-hover:from-cyan-500 group-hover:to-blue-500 hover:text-white  focus:ring-4 focus:outline-none focus:ring-cyan-200 dark:focus:ring-cyan-800">

                       <span
                           className="relative px-5 py-2.5 transition-all ease-in duration-75 bg-black text-white  rounded-md group-hover:bg-opacity-0">
                   Suivant
</span>
                    </button>

                </StyledLineAction>
            </StyledFace>
        );
    };

    return (
        <>
            {cards.length > 0 && cardIndex !== null ? (
                <StyledFlashCard key={cardIndex}>
                    {face === 'recto' ? (
                        <Recto question={cards[cardIndex].question}/>
                    ) : (
                        <Verso answer={cards[cardIndex].answer}/>
                    )}
                </StyledFlashCard>
            ) : (
                <div>
                    <h1>Aucune carte à afficher</h1>
                </div>
            )}
        </>
    );
};
