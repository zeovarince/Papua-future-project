(() => {
  const batchSelect = document.querySelector('#batch_id');
  const questionGroups = [...document.querySelectorAll('[data-custom-questions]')];
  if (!batchSelect || questionGroups.length === 0) return;

  const updateQuestionGroups = () => {
    const selectedType = batchSelect.selectedOptions[0]?.dataset.batchType || '';
    questionGroups.forEach((group) => {
      const isVisible = group.dataset.applicationType === selectedType;
      group.hidden = !isVisible;
      group.querySelectorAll('input, textarea, select').forEach((field) => {
        field.disabled = !isVisible;
      });
    });
  };

  batchSelect.addEventListener('change', updateQuestionGroups);
  updateQuestionGroups();
})();
