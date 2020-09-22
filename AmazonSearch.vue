<template>
	<div class="pa-4">
		<div>
			<div class="float-left">

			</div>
			<div class="float-right">

			</div>
		</div>

		<v-container fluid>
			<v-row no-gutters>
				<v-col
					cols="auto"
					class="mr-auto"
				>
					<div class="display-1 mb-4 font-weight-light">Amazon Search</div>
				</v-col>

				<v-col cols="auto">
					<select-marketplace />
				</v-col>
			</v-row>
		</v-container>
		<div class="mb-6">
			<v-combobox
				v-model="amazonAutocomplete"
				:items="suggestions"
				:loading="amazonAutocompleteLoading"
				:search-input.sync="search"
				clearable
				hide-no-data
				hide-details
				item-text="value"
				label="Enter keyword or ASIN"
				filled
				append-icon
				no-filter
				clear-icon="fa-times"
				@click:clear="clearSuggestions"
				:menuProps="{
					openOnClick: false
				}"
			></v-combobox>
		</div>

		<v-sheet
			width="100%"
			elevation="2"
			light
			v-if="amazonResultsLoading"
		>
			<v-skeleton-loader
				class="mb-6"
				type="table-thead, table-tbody"
			></v-skeleton-loader>
		</v-sheet>

		<asin-table
			v-if="displayResults"
			:results="results"
		></asin-table>
	</div>
</template>


<script>
import { apiCall, apiRoutes } from "@/utils/api";
import AsinTable from "../components/AsinTable.vue";
import SelectMarketplace from "../components/SelectMarketplace.vue";

export default {
	name: "AmazonSearch",
	metaInfo: {
		title: "Amazon Search"
	},
	components: {
		AsinTable,
		SelectMarketplace
	},
	data () {
		return {
			amazonAutocompleteLoading: false,
			amazonResultsLoading: false,
			displayResults: false,
			suggestions: [],
			amazonAutocomplete: null,
			search: null,
			results: []
		};
	},

	computed: {
		currentMarketplace () {
			return this.$store.getters.getCurrentMarketplace;
		},
		marketplaceOptions () {
			return this.$store.getters.getMaketplaceOptions;
		}
	},

	beforeCreate () {
		this.$store.dispatch('setCurrentMarketplace');
	},

	watch: {
		amazonAutocompleteLoading (val) {
			if (val) {
				this.displayResults = false;
			}
		},
		amazonAutocomplete (val) {
			console.log(val);
			console.log(this.search);
			const x = 1;
			if (x === 1) {
				// if (val != null) {
				this.amazonResultsLoading = true;

				/** Search */
				apiCall({
					url: apiRoutes.amazon.search + "?query=" + val,
					method: "get"
				})
					.then(resp => {
						this.displayResults = true;

						if (resp.success) {
							this.results = resp.data;
						}
					})
					.catch(err => {
						console.log(err);
					})
					.finally(() => {
						this.amazonResultsLoading = false;
					});
			}
		},
		search (val) {
			if (!val) return;

			if (val.length < 3) return;

			// Items have already been loaded
			// if (this.suggestions.length > 0) return;

			// Items have already been requested
			if (this.amazonAutocompleteLoading) return;

			this.amazonAutocompleteLoading = true;

			/** Autocomplete suggestions */
			apiCall({
				url: apiRoutes.amazon.autocomplete + "?query=" + val,
				method: "get"
			})
				.then(resp => {
					if (resp.success) {
						this.suggestions = resp.data;
					}
				})
				.catch(err => {
					console.log(err);
				})
				.finally(() => {
					this.amazonAutocompleteLoading = false;
				});
		}
	},

	methods: {
		clearSuggestions () {
			this.suggestions = [];
			this.amazonAutocomplete = null;
		}
	}
};
</script>
