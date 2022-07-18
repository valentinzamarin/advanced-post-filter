class APF_Public_Scripts {
  constructor() {
    document.addEventListener("DOMContentLoaded", () => {
      document.querySelectorAll(".apf-filter").forEach((form) => {
        form.addEventListener("change", (event) => {
          this.formHandler(event);
        });
      });
      document.querySelectorAll(".apf-page").forEach((button) => {
        button.addEventListener("click", (event) => {
          this.paginationHandler(event);
        });
      });

      this.filterObserver();
    });
  }

  filterObserver() {
    
    let observer = new MutationObserver((mutationRecords) => {
      document.querySelectorAll(".apf-page").forEach((button) => {
        button.addEventListener("click", (event) => {
          this.paginationHandler(event);
        });
      });
    });
    document.querySelectorAll(".apf-response").forEach( ( item ) => {
      observer.observe(item, {
        childList: true,
        subtree: true,
        characterDataOldValue: true,
      });
    })
    
  }

  formHandler(event) {
    event.preventDefault();

    let $this = event.target,
      value = $this.value;

    let form = $this.closest("form"),
      filterId = form.dataset.filter,
      wrapper = form.nextElementSibling;

    wrapper.style.opacity = ".5";

    let categories = this.getCategories(form);

    let data = new FormData();
    data.append("action", "afp_get_posts");
    data.append("nonce", apf.nonce);
    data.append("id", filterId);
    data.append("categories", categories);

    const admin_ajax_url = apf.ajax_url;
    fetch(admin_ajax_url, {
      method: "post",
      body: data,
    })
      .then((response) => {
        return response.json();
      })
      .then((res) => {
        wrapper.innerHTML = res.data.content;
      })
      .catch((err) => {
        console.log(err);
      })
      .finally(() => {
        wrapper.style.opacity = "1";
      });
  }

  paginationHandler(event) {
    event.preventDefault();

    let $this = event.target,
      page = $this.dataset.page;


    let wrapper = $this.closest(".apf-response")
    wrapper.style.opacity = ".5";

    let form = wrapper.previousElementSibling,
      filterId = form.dataset.filter;

    let categories = this.getCategories(form);

    let data = new FormData();
    data.append("action", "afp_get_posts");
    data.append("nonce", apf.nonce);
    data.append("id", filterId);
    data.append("page", page);
    data.append("categories", categories);

    const admin_ajax_url = apf.ajax_url;
    fetch(admin_ajax_url, {
      method: "post",
      body: data,
    })
      .then((response) => {
        return response.json();
      })
      .then((res) => {
        wrapper.innerHTML = res.data.content;
      })
      .catch(() => {
        console.log("error");
      })
      .finally(() => {
        wrapper.style.opacity = "1";
      });
  }

  getCategories(form) {
    let categories = [];
    form.querySelectorAll("input:checked").forEach((item) => {
      categories.push(item.value);
    });

    if (categories.length === 0) {
      return (categories = []);
    }

    return categories;
  }
}

new APF_Public_Scripts();
