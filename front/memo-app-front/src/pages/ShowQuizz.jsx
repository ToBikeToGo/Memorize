import React, { useEffect, useState } from 'react';

import { FlashCard } from '../components/FlashCard';
import { API_URL } from '../helpers/getApiUrl';

export const ShowQuizz = () => {
  const [cards, setCards] = useState([]);
  const [cardIndex, setCardIndex] = useState(0);

  useEffect(() => {
    const fetchData = async () => {
    try {
        const response = await fetch(`${API_URL}/cards`);
        const data = await response.json();
        setCards(data);
        if (data.length > 0) {
          setCardIndex(0);
        } else {
          setCardIndex(null);
        }
      } catch (error) {
        console.log(`Error: ${error}`);
      }
    };

    fetchData();
  }, []);

  const handleNext = () => {
    if (cardIndex < cards.length - 1) {
      setCardIndex(cardIndex + 1);
    }
  };

  const handlePrevious = () => {
    if (cardIndex > 0) {
      setCardIndex(cardIndex - 1);
    }
  };

  const handleCorrect = (correct) => {
    const card = cards[cardIndex];
    fetch(`${API_URL}/cards/` + card.id + '/answer', {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        isValid: correct,
      }),
    });
    handleNext();
  };

  return (
    <div className={'p-10'}>
      {cards.length > 0 && cardIndex !== null ? (
        <>
          <FlashCard
            cards={cards}
            cardIndex={cardIndex}
            onCorrect={handleCorrect}
            onPrevious={handlePrevious}
            onNext={handleNext}
          />{' '}
        </>
      ) : (
        <p> Chargement des cartes ou aucune carte disponible. </p>
      )}{' '}
    </div>
  );
};
